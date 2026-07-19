<?php

namespace App\Livewire\Admin\Event;

use App\Models\Event;
use Livewire\Component;
use Livewire\WithPagination;
use App\Mail\EventBroadcastMail;
use App\Models\BroadcastHistory;
use Illuminate\Support\Facades\Mail;
use App\Models\Registration;
use App\Jobs\SendEventBroadcast;
use App\Exports\RegistrantsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Mail\FeedbackInvitationMail;
use App\Models\BroadcastTemplate;
use Livewire\Attributes\On;
use Illuminate\Support\Str;
use Exception;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use App\Exports\CheckinHistoryExport;
use App\Traits\HandlesCheckin;


#[Layout('layouts.app')]
class Registrants extends Component
{
    use WithPagination, HandlesCheckin;

    public $search = '';
    public Event $event;
    public $broadcastSubject = '';
    public $broadcastContent = '';

    public $selectedRegistrants = [];
    public $selectAll = false;

    public $showDetailModal = false;
    public $selectedRegistrantForDetail;

    public $showExportModal = false;
    public $availableColumns = [];
    public $selectedColumns = [];

    public $templates;
    public $selectedDate;

    public $filterType = 'all';

    public function mount(Event $event)
    {
        $this->event = $event;
        $this->templates = $this->event->broadcastTemplates;

        // --- LOGIKA BARU UNTUK MENGATUR TANGGAL DEFAULT ---
        $eventStartDate = Carbon::parse($this->event->start_date);
        $eventEndDate = Carbon::parse($this->event->end_date);

        // Jika event sedang berlangsung, set tanggal ke hari ini
        if (today()->between($eventStartDate, $eventEndDate)) {
            $this->selectedDate = today()->toDateString();
        } else {
            // Jika event sudah lewat atau belum mulai, set ke hari pertama event
            $this->selectedDate = $eventStartDate->toDateString();
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function showDetails($registrationId)
    {
        // Ambil data pendaftar lengkap dengan relasi checkinLogs
        $this->selectedRegistrantForDetail = Registration::with('checkinLogs')->findOrFail($registrationId);
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedRegistrantForDetail = null; // Reset data
    }

    #[On('delete-registration')]
    public function destroyRegistration($registrationId)
    {
        if ($registration = Registration::find($registrationId)) {
            try {
                // Hapus data terkait terlebih dahulu (Cleanup)
                $registration->checkinLogs()->delete();
                $registration->broadcastHistories()->delete();
                
                // Pastikan transaksi juga ditangani jika ada (morphOne)
                if ($registration->transaction) {
                    $registration->transaction()->delete();
                }

                $registration->delete();
                $this->dispatch('registration-deleted', message: 'Registrant has been successfully removed from history.');
            } catch (Exception $e) {
                $this->dispatch('delete-failed', message: 'Database Error: ' . $e->getMessage());
            }
        } else {
            $this->dispatch('delete-failed', message: 'Error: Node registration not found.');
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            // Jika dicentang, ambil semua ID pendaftar di halaman saat ini
            $this->selectedRegistrants = $this->event->registrations()
                ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                ->pluck('id')->map(fn($id) => (string)$id);
        } else {
            // Jika tidak dicentang, kosongkan pilihan
            $this->selectedRegistrants = [];
        }
    }

    public function render()
    {
        // 1. Base Query & Eager Loading
        // Kita muat 'user' dan 'checkinLogs' (hanya untuk tanggal yang dipilih)
        $query = $this->event->registrations()
            ->with([
                'user',
                'checkinLogs' => function ($q) {
                    $date = \Carbon\Carbon::parse($this->selectedDate);
                    $q->whereBetween('checkin_time', [$date->copy()->startOfDay(), $date->copy()->endOfDay()]);
                }
            ]);

        // 2. Logika Pencarian (Search)
        // Mencakup Nama, Email, HP, Data JSON, dan Nama User terkait
        $query->where(function ($q) {
            $q->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('email', 'like', '%' . $this->search . '%')
                ->orWhere('phone_number', 'like', '%' . $this->search . '%')
                ->orWhere('data', 'like', '%' . $this->search . '%')
                ->orWhereHas('user', function ($subQuery) {
                    $subQuery->where('name', 'like', '%' . $this->search . '%');
                });
        });

        // 3. Logika Filter (Invited vs Regular vs Payment Status)
        if ($this->filterType === 'invited') {
            $query->where('data->source', 'Invitation System');
        } elseif ($this->filterType === 'regular') {
            $query->where(function ($q) {
                $q->whereNull('data->source')
                    ->orWhere('data->source', '!=', 'Invitation System');
            });
        } elseif ($this->filterType === 'paid') {
            $query->where('payment_status', 'paid');
        } elseif ($this->filterType === 'unpaid') {
            $query->where('payment_status', 'unpaid');
        }

        // 4. Eksekusi Query & Pagination
        $registrants = $query->latest()->paginate(10);

        return view('livewire.admin.event.registrants', [
            'registrants' => $registrants,
        ]);
    }

    public function sendBroadcast()
    {
        $this->validate([
            'broadcastSubject' => 'required|string',
            'broadcastContent' => 'required|string',
            'selectedRegistrants' => 'required|array|min:1'
        ], [
            'selectedRegistrants.required' => 'Please select at least one registrant.'
        ]);

        // Menggunakan with('user') untuk performa lebih baik (Eager Loading)
        $recipients = Registration::with('user')->whereIn('id', $this->selectedRegistrants)->get();
        $successfulSends = 0; // Penghitung email yang berhasil terkirim

        foreach ($recipients as $recipient) {
            try {
                // --- MULAI BLOK PERUBAHAN ---

                // 1. Siapkan data asli untuk mengganti placeholder
                $isCertActive = ($this->event->certificate_config['is_active'] ?? false);
                $placeholders = [
                    '[nama_peserta]'      => $recipient->user->name ?? $recipient->name, // Mengambil nama dari relasi user dulu
                    '[nama_event]'        => $this->event->name,
                    '[link_event_detail]' => route('events.show', $this->event),
                    '[nama_instansi]'     => $recipient->user->nama_instansi ?? $recipient->data['nama_instansi'] ?? 'N/A',
                    '[jabatan]'           => $recipient->user->jabatan ?? $recipient->data['jabatan'] ?? 'N/A',
                    '[link_sertifikat]'   => ($isCertActive && ($recipient->checked_in_at || $recipient->checkinLogs()->count() > 0)) ? route('public.certificate.show', $recipient->uuid) : '-',
                    '[link_e_tiket]'      => route('tickets.qrcode', $recipient->uuid),
                    '[link_check_in]'     => route('checkin.scan', $recipient->uuid),
                    '[link_feedback]'     => $this->event->is_feedback_active ? route('feedback.show', ['event' => $this->event, 'registration' => $recipient->uuid]) : '#',
                ];

                // 2. Ganti placeholder di subject dan content
                $finalSubject = str_replace(array_keys($placeholders), array_values($placeholders), $this->broadcastSubject);
                $finalContent = str_replace(array_keys($placeholders), array_values($placeholders), $this->broadcastContent);

                // 3. Logika untuk tombol aksi dinamis
                $isOnlineAttendance = ($this->event->type === 'online') ||
                    ($this->event->type === 'hybrid' && $recipient->attendance_type === 'online');

                if ($isOnlineAttendance) {
                    $actionUrl = $this->event->meeting_link;
                    $actionText = 'Gabung Event Online';
                } else {
                    $actionUrl = route('tickets.qrcode', $recipient->uuid);
                    $actionText = 'Lihat E-Tiket Anda';
                }

                // Buat HTML untuk tombol dan ganti placeholder [tombol_aksi]
                $actionButtonHtml = '<a href="' . $actionUrl . '" style="background-color: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">' . $actionText . '</a>';
                $finalContent = str_replace('[tombol_aksi]', $actionButtonHtml, $finalContent);

                // 4. Kirim email dengan konten yang sudah diubah
                Mail::to($recipient->user->email ?? $recipient->email)->send(new EventBroadcastMail($finalSubject, $finalContent, $this->event->organizer));

                // 5. JIKA EMAIL BERHASIL, CATAT KE RIWAYAT
                BroadcastHistory::create([
                    'registration_id' => $recipient->id,
                    'event_id'        => $this->event->id,
                    'subject'         => $finalSubject,
                    'content'         => $finalContent,
                ]);

                $successfulSends++; // Tambah hitungan jika berhasil

                // --- SELESAI BLOK PERUBAHAN ---

            } catch (Exception $e) {
                // Jika terjadi error, catat di log (opsional) dan lanjut ke penerima berikutnya
                // Log::error('Failed to send broadcast to ' . $recipient->id . ': ' . $e->getMessage());
                continue;
            }
        }

        // Reset form
        $this->reset(['broadcastSubject', 'broadcastContent', 'selectedRegistrants', 'selectAll']);

        // Tampilkan pesan sukses dengan jumlah yang akurat
        session()->flash('message', 'Email broadcast has been sent to ' . $successfulSends . ' of ' . $recipients->count() . ' selected registrants.');
    }

    public function toggleCheckIn($registrationId)
    {
        $registration = Registration::findOrFail($registrationId);

        // MODIFIKASI: Ganti 'today()' dengan '$this->selectedDate'
        $logForSelectedDate = $registration->checkinLogs()
            ->whereDate('checkin_time', $this->selectedDate)
            ->first();

        if ($logForSelectedDate) {
            // Jika sudah ada, hapus log untuk tanggal yang dipilih (Undo Check-in)
            $logForSelectedDate->delete();
            session()->flash('message', "Check-in for " . $registration->name . " on " . $this->selectedDate . " has been undone.");
        } else {
            // Selalu panggil performCheckIn untuk memicu notifikasi & log (untuk hari ini)
            $this->performCheckIn($registration, $this->event);
            
            // Jika admin sedang melihat tanggal lain (bukan hari ini), tetap buatkan log untuk tanggal tersebut
            if ($this->selectedDate !== today()->toDateString()) {
                $checkinTimestamp = \Carbon\Carbon::parse($this->selectedDate)->startOfDay()->addHours(9);
                $registration->checkinLogs()->firstOrCreate(['checkin_time' => $checkinTimestamp]);
            }
            
            session()->flash('message', $registration->name . ' has been checked in.');
        }
    }

    public function resendWhatsapp($registrationId)
    {
        $registration = Registration::findOrFail($registrationId);

        if (!$registration->phone_number) {
            $this->dispatch('delete-failed', message: 'Error: Participant does not have a phone number.');
            return;
        }

        if (!$this->event->confirmation_template_id) {
            $this->dispatch('delete-failed', message: 'Error: No confirmation template set for this event.');
            return;
        }

        $template = \App\Models\EventEmailTemplate::find($this->event->confirmation_template_id);
        
        if (!$template || (!$template->whatsapp_template_id && !$template->whatsapp_content)) {
            $this->dispatch('delete-failed', message: 'Error: Confirmation template or WhatsApp content not found.');
            return;
        }

        try {
            $whatsapp = new \App\Services\WhatsAppService($this->event->organizer_id);
            $parser = app(\App\Services\MessageParserService::class);
            
            if ($template->whatsapp_template_id && $template->whatsappTemplate) {
                $whatsappTemplate = $template->whatsappTemplate;
                
                $bodyParams = [];
                if (isset($whatsappTemplate->parameters['body'])) {
                    $bodyParams = $parser->parseParameters($whatsappTemplate->parameters['body'], $registration);
                }

                $headerParam = null;
                if (isset($whatsappTemplate->parameters['header']) && $whatsappTemplate->parameters['header']) {
                    $headerType = $whatsappTemplate->parameters['header']['type'];
                    $headerKey = $whatsappTemplate->parameters['header']['value'];
                    $resolvedVal = $parser->parseParameters([$headerKey], $registration)[0] ?? null;
                    if (!empty($template->whatsapp_header_media_path) && in_array($headerType, ['image', 'video', 'document'])) {
                        $resolvedVal = asset('storage/' . $template->whatsapp_header_media_path);
                    }
                    if ($resolvedVal) {
                        $headerParam = [
                            'type' => $headerType,
                            'value' => $resolvedVal,
                            'filename' => 'Tiket.pdf'
                        ];
                    }
                }

                $buttonParams = [];
                if (isset($whatsappTemplate->parameters['buttons'])) {
                    $customMapping = $template->whatsapp_buttons_mapping ?? [];
                    foreach ($whatsappTemplate->parameters['buttons'] as $btn) {
                        $idx = $btn['index'];
                        $mapVal = $customMapping[$idx] ?? ($btn['value'] ?? null);
                        if ($mapVal) {
                            $resolvedBtnVal = $parser->parseParameters([$mapVal], $registration)[0] ?? null;
                            if ($resolvedBtnVal) {
                                if ($mapVal === 'ticket_url' || $mapVal === 'payment_link') {
                                    $resolvedBtnVal = $registration->uuid;
                                }
                                $buttonParams[] = [
                                    'index' => $idx,
                                    'value' => $resolvedBtnVal
                                ];
                            }
                        }
                    }
                }

                $payloadParams = [
                    'header' => $headerParam,
                    'body' => $bodyParams,
                    'buttons' => $buttonParams
                ];

                $whatsapp->sendTemplateMessage(
                    $registration->phone_number,
                    $whatsappTemplate->name,
                    $whatsappTemplate->language_code,
                    $payloadParams
                );
            } else {
                $parsed = $parser->parseForWhatsApp($template->whatsapp_content, $registration, $template);
                if ($parsed['attachment_url']) {
                    $whatsapp->sendFile($registration->phone_number, $parsed['message'], $parsed['attachment_url'], $parsed['fallback_url'], 'ticket_qr.png');
                } else {
                    $whatsapp->sendMessage($registration->phone_number, $parsed['message']);
                }
            }
            
            $this->dispatch('registration-deleted', message: 'WhatsApp Ticket has been successfully resent to ' . $registration->phone_number);
        } catch (\Exception $e) {
            $this->dispatch('delete-failed', message: 'WhatsApp Error: ' . $e->getMessage());
        }
    }

    public function resendWhatsappWeb($registrationId)
    {
        $registration = Registration::findOrFail($registrationId);

        if (!$registration->phone_number) {
            $this->dispatch('delete-failed', message: 'Error: Participant does not have a phone number.');
            return;
        }

        if (!$this->event->confirmation_template_id) {
            $this->dispatch('delete-failed', message: 'Error: No confirmation template set for this event.');
            return;
        }

        $template = \App\Models\EventEmailTemplate::find($this->event->confirmation_template_id);
        
        if (!$template || !$template->whatsapp_content) {
            $this->dispatch('delete-failed', message: 'Error: Confirmation template or WhatsApp content not found.');
            return;
        }

        try {
            $parser = app(\App\Services\MessageParserService::class);
            $parsed = $parser->parseForWhatsApp($template->whatsapp_content, $registration, $template);
            
            $phone = preg_replace('/[^0-9]/', '', $registration->phone_number);
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            }
            
            $text = urlencode($parsed['message']);
            
            // Note: If there's an attachment URL, we can append it to the message or just send text
            if (!empty($parsed['attachment_url'])) {
                $text .= urlencode("\n\nTiket / QR Code: " . $parsed['attachment_url']);
            }
            
            $url = "https://wa.me/{$phone}?text={$text}";
            
            $this->js("window.open('{$url}', '_blank')");
            
        } catch (\Exception $e) {
            $this->dispatch('delete-failed', message: 'WhatsApp Error: ' . $e->getMessage());
        }
    }

    public function sendReminderWhatsappWeb($registrationId)
    {
        $registration = Registration::findOrFail($registrationId);

        if (!$registration->phone_number) {
            $this->dispatch('delete-failed', message: 'Error: Participant does not have a phone number.');
            return;
        }

        if (!$this->event->reminder_template_id) {
            $this->dispatch('delete-failed', message: 'Error: No reminder template set for this event.');
            return;
        }

        $template = \App\Models\EventEmailTemplate::find($this->event->reminder_template_id);
        
        if (!$template || !$template->whatsapp_content) {
            $this->dispatch('delete-failed', message: 'Error: Reminder template or WhatsApp content not found.');
            return;
        }

        try {
            $parser = app(\App\Services\MessageParserService::class);
            $parsed = $parser->parseForWhatsApp($template->whatsapp_content, $registration, $template);
            
            $phone = preg_replace('/[^0-9]/', '', $registration->phone_number);
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            }
            
            $text = urlencode($parsed['message']);
            
            // Note: If there's an attachment URL, we can append it to the message or just send text
            if (!empty($parsed['attachment_url'])) {
                $text .= urlencode("\n\nTiket / QR Code: " . $parsed['attachment_url']);
            }
            
            $url = "https://wa.me/{$phone}?text={$text}";
            
            $this->js("window.open('{$url}', '_blank')");
            
        } catch (\Exception $e) {
            $this->dispatch('delete-failed', message: 'WhatsApp Error: ' . $e->getMessage());
        }
    }

    public function resendTicket($registrationId)
    {
        $registration = Registration::findOrFail($registrationId);

        if (!$this->event->confirmation_template_id) {
            $this->dispatch('delete-failed', message: 'Error: No confirmation template set for this event.');
            return;
        }

        $template = \App\Models\EventEmailTemplate::find($this->event->confirmation_template_id);
        
        if (!$template) {
            $this->dispatch('delete-failed', message: 'Error: Confirmation template not found.');
            return;
        }

        try {
            // Pengiriman email tunggal (Synchronous send for instant feedback)
            Mail::to($registration->email)->send(new \App\Mail\DynamicBroadcastMail($template, $registration));
            
            $this->dispatch('registration-deleted', message: 'Ticket has been successfully resent to ' . $registration->email);
        } catch (\Exception $e) {
            $this->dispatch('delete-failed', message: 'Mail Error: ' . $e->getMessage());
        }
    }

    public function sendFeedbackLink($registrationId)
    {
        $registration = Registration::findOrFail($registrationId);

        if (!$registration->checked_in_at) {
            session()->flash('message', 'Error: Participant has not checked in yet.');
            return;
        }

        if ($registration->feedback_email_sent_at) {
            session()->flash('message', 'Info: Feedback link has already been sent to this participant.');
            return;
        }

        // 1. Determine Template
        $template = null;
        if ($this->event->feedback_template_id) {
            $template = \App\Models\EventEmailTemplate::find($this->event->feedback_template_id);
        }

        if (!$template) {
            $template = \App\Models\EventEmailTemplate::where(function($q) {
                $q->where('event_id', $this->event->id)->orWhereNull('event_id');
            })
            ->where('category', 'event_feedback')
            ->first();
        }

        if (!$template) {
            session()->flash('message', 'Error: No Feedback Template found for this event.');
            return;
        }

        // 2. Send via Email
        \Illuminate\Support\Facades\Mail::to($registration->email)->send(new \App\Mail\DynamicBroadcastMail($template, $registration));

        // 3. Mark as Sent
        $registration->update(['feedback_email_sent_at' => now()]);

        session()->flash('message', 'Feedback invitation has been sent to ' . $registration->name);
    }

    public function saveTemplate()
    {
        $this->validate([
            'broadcastSubject' => 'required|string|max:255',
            'broadcastContent' => 'required|string',
        ]);

        // Logika "Update or Create" yang kita diskusikan
        $this->event->broadcastTemplates()->updateOrCreate(
            ['subject' => $this->broadcastSubject], // Kondisi untuk mencari
            ['content' => $this->broadcastContent]  // Data untuk diupdate atau dibuat
        );

        $this->templates = $this->event->broadcastTemplates()->get(); // Refresh daftar template
        session()->flash('message', 'Template "' . $this->broadcastSubject . '" has been saved.');
    }

    public function loadTemplate($templateId)
    {
        $template = BroadcastTemplate::findOrFail($templateId);
        $this->broadcastSubject = $template->subject;
        $this->broadcastContent = $template->content;

        // Kirim event ke browser untuk update CKEditor
        $this->dispatch('template-loaded', content: $this->broadcastContent);
        session()->flash('message', 'Template "' . $template->subject . '" has been loaded.');
    }

    public function deleteTemplate($templateId)
    {
        // Pastikan template yang dihapus milik event ini demi keamanan
        BroadcastTemplate::where('id', $templateId)->where('event_id', $this->event->id)->delete();
        $this->templates = $this->event->broadcastTemplates()->get(); // Refresh daftar template
        session()->flash('message', 'Template has been deleted.');
    }

    // --- METHOD BARU UNTUK EXPORT ---
    public function openExportModal()
    {
        // 1. Definisikan kolom statis/standar
        $staticColumns = [
            'name' => 'Nama',
            'email' => 'Email',
            'phone_number' => 'No. Telepon',
            'registered_at' => 'Tanggal Daftar',
            'rfid_registered_at' => 'Tanggal Registrasi RFID', // <-- TAMBAHAN BARU
            'status' => 'Status / Tipe',
        ];

        // 2. Ambil semua key unik dari data dinamis (form & profile)
        $registrants = $this->event->registrations()->with('user')->get();
        $dynamicKeys = [];

        foreach ($registrants as $registrant) {
            if (is_array($registrant->data)) {
                $dynamicKeys = array_merge($dynamicKeys, array_keys($registrant->data));
            }
            if ($registrant->user && is_array($registrant->user->profile_data)) {
                $dynamicKeys = array_merge($dynamicKeys, array_keys($registrant->user->profile_data));
            }
        }

        $formattedDynamicColumns = [];
        foreach (array_unique($dynamicKeys) as $key) {
            $formattedDynamicColumns[$key] = Str::title(str_replace('_', ' ', $key));
        }

        // 3. Gabungkan semua kolom yang tersedia
        $this->availableColumns = array_merge($staticColumns, $formattedDynamicColumns);

        // 4. Pilih beberapa kolom umum sebagai default
        // --- DIUBAH untuk menyertakan kolom baru ---
        $this->selectedColumns = ['name', 'email', 'phone_number', 'registered_at', 'rfid_registered_at', 'status'];

        $this->showExportModal = true;
    }

    // FUNGSI BARU UNTUK MENUTUP MODAL EXPORT
    public function closeExportModal()
    {
        $this->showExportModal = false;
    }

    // GANTI NAMA FUNGSI 'export()' MENJADI 'exportSelected()'
    public function exportSelected()
    {
        // Validasi: pastikan setidaknya satu kolom dipilih
        if (empty($this->selectedColumns)) {
            // Anda bisa menambahkan pesan error di sini jika perlu
            return;
        }

        $fileName = 'registrants-' . $this->event->slug . '-' . now()->format('Y-m-d') . '.xlsx';
        // $this->closeExportModal();
        // $this->dispatch('export-success');

        // Kirim event ID dan kolom yang dipilih ke class Export
        // Mengirim ID lebih aman untuk proses antrian (queue) daripada seluruh objek
        return Excel::download(new RegistrantsExport($this->event, $this->selectedColumns), $fileName);
    }

    public function exportCheckinHistory()
    {
        // 1. Tentukan nama file
        $fileName = 'checkin-history-' . $this->event->slug . '-' . now()->format('Y-m-d') . '.xlsx';

        // 2. Panggil dan download Class Export BARU kita.
        // Kita meneruskan $this->event agar class-nya tahu event mana yang harus diekspor.
        return Excel::download(new CheckinHistoryExport($this->event), $fileName);
    }
}
