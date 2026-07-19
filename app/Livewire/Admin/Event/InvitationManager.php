<?php

namespace App\Livewire\Admin\Event;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Event;
use App\Models\Invitation;
use App\Imports\InvitationsImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvitationMail;
use App\Exports\InvitationTemplateExport;
use App\Models\Registration;
use App\Jobs\SendInvitationJob;


class InvitationManager extends Component
{
    use WithFileUploads, WithPagination;

    public Event $event;
    public $file;

    // Template Pesan
    public $waTemplate;
    public $emailSubject;
    public $emailBody;
    public $confirmGreeting;
    public $invitationEmailBanner; // New banner upload
    public ?string $existingEmailBannerPath = null;

    // Duplication Handling
    public $duplicateEmails = [];
    public $uniqueRowsToImport = [];
    public $showDuplicateModal = false;


    // Filter & Search
    public $filterStatus = 'all';
    public $search = '';

    public $isEditing = false;
    public $editingInvitationId;
    public $editForm = [
        'name' => '',
        'email' => '',
        'phone_number' => '',
        'company' => '',
        'category' => '',
    ];

    public $letterBody = '';
    public $newLetterHeader; // Upload sementara
    public $existingLetterHeader; // Path dari DB
    public $newAttachments = []; // Upload sementara (multiple)
    public $existingAttachments = []; // Array Path dari DB

    public $confirmingHeaderDeletion = false;
    public $confirmingInvitationDeletion = false;
    public $invitationIdToDelete;

    public $registrationToDelete = null;

    public function mount(Event $event)
    {
        $this->event = $event;

        // Default Template WA
        $this->waTemplate = $this->event->invitation_wa_template ?: "Halo *{name}*, 👋

Kami mengundang Anda untuk menghadiri acara *{event_name}* yang akan diselenggarakan dalam waktu dekat. Merupakan suatu kehormatan bagi kami jika Anda dapat hadir.

Silakan akses Surat Undangan Digital Anda melalui tautan di bawah ini:
🔗 {link_surat}

Mohon kesediaan Anda untuk melakukan Konfirmasi Kehadiran melalui tautan berikut:
✅ {link_konfirmasi}

Terima kasih atas perhatian dan kerja samanya.";

        // Default Template Email
        $this->emailSubject = $this->event->invitation_email_subject
            ?? "Undangan: " . $event->name;

        $this->emailBody = $this->event->invitation_email_body
            ?? "Kami mengundang Anda untuk hadir di acara kami. Silakan klik tombol di bawah ini untuk konfirmasi kehadiran Anda.";

        $this->letterBody = $this->event->invitation_letter_body ?: "
            <p><strong>Kepada Yth.</strong></p>
            <p><strong>{name}</strong></p>
            <p>{company}</p>
            <p>&nbsp;</p>
            <p>Dengan hormat,</p>
            <p>Merupakan suatu kehormatan bagi kami untuk mengundang Anda hadir dalam acara <strong>{event_name}</strong>. Acara ini diselenggarakan sebagai bentuk apresiasi dan kolaborasi kita bersama.</p>
            <p>Besar harapan kami Anda dapat meluangkan waktu untuk hadir dan berpartisipasi dalam rangkaian acara yang telah kami siapkan.</p>
            <p>Untuk memudahkan koordinasi, mohon kesediaan Anda untuk mengonfirmasi kehadiran melalui tautan di bawah ini:</p>
            <p style='text-align: center;'>{link_konfirmasi}</p>
            <p>&nbsp;</p>
            <p>Atas perhatian dan kehadirannya, kami ucapkan terima kasih.</p>
            <p>&nbsp;</p>
            <p>Hormat kami,</p>
            <p><strong>Panitia Pelaksana</strong></p>
        ";
        $this->existingLetterHeader = $this->event->invitation_letter_header;
        $this->existingEmailBannerPath = $this->event->invitation_email_banner;
        $this->existingAttachments = $this->event->invitation_files ?? [];
        $this->confirmGreeting = $this->event->invitation_confirm_greeting ?: "Halo, <strong>{name}</strong> ({company}).\nMohon berikan konfirmasi kehadiran Anda di bawah ini:";
    }

    // Method untuk membuka modal konfirmasi
    public function confirmDeleteInvitation($id)
    {
        $this->invitationIdToDelete = $id;
        $invitation = Invitation::find($id);

        // Cek apakah email undangan ini sudah terdaftar di registrasi event ini
        if ($invitation && $invitation->email) {
            $this->registrationToDelete = Registration::where('event_id', $this->event->id)
                ->where('email', $invitation->email)
                ->first();
        } else {
            $this->registrationToDelete = null;
        }

        $this->confirmingInvitationDeletion = true;
    }
    // Method eksekusi hapus
    public function deleteInvitation($withRegistration = false)
    {
        $invitation = Invitation::find($this->invitationIdToDelete);

        if ($invitation) {
            // Logika Hapus Registrasi (Opsional)
            if ($withRegistration && $this->registrationToDelete) {
                Registration::where('id', $this->registrationToDelete->id)->delete();
                $msg = 'Data undangan dan data peserta (registrant) berhasil dihapus.';
            } else {
                $msg = 'Data undangan berhasil dihapus.';
            }

            $invitation->delete();
            session()->flash('message', $msg);
        } else {
            session()->flash('error', 'Gagal menghapus data.');
        }

        // Reset state
        $this->confirmingInvitationDeletion = false;
        $this->invitationIdToDelete = null;
        $this->registrationToDelete = null;
    }

    // Method batal hapus
    public function cancelDeleteInvitation()
    {
        $this->confirmingInvitationDeletion = false;
        $this->invitationIdToDelete = null;
        $this->registrationToDelete = null; // Reset juga yang ini
    }

    // ▼▼▼ METHOD UNTUK SIMPAN PENGATURAN SURAT ▼▼▼
    public function saveLetterSettings()
    {
        $this->validate([
            'letterBody' => 'nullable|string',
            'newLetterHeader' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'newAttachments.*' => 'nullable|file|max:10240',
        ], [
            'newLetterHeader.image' => 'Kop surat harus berupa gambar.',
            'newLetterHeader.mimes' => 'Format kop surat harus jpeg, jpg, atau png.',
            'newLetterHeader.max' => 'Ukuran kop surat maksimal 2MB.',
            'newAttachments.*.max' => 'Ukuran setiap lampiran maksimal 10MB.',
            'newAttachments.*.file' => 'Lampiran harus berupa file valid.',
        ]);

        $dataToUpdate = [
            'invitation_letter_body' => $this->letterBody,
        ];

        // 1. Handle Upload Kop Surat (Replace)
        if ($this->newLetterHeader) {
            $path = $this->newLetterHeader->store('event-assets/' . $this->event->id, 'public');
            $dataToUpdate['invitation_letter_header'] = $path;
            $this->existingLetterHeader = $path; // Update view
        }

        // 2. Handle Upload Lampiran (Append)
        if (!empty($this->newAttachments)) {
            $currentFiles = $this->existingAttachments;
            foreach ($this->newAttachments as $file) {
                $currentFiles[] = $file->store('event-docs/' . $this->event->id, 'public');
            }
            $dataToUpdate['invitation_files'] = $currentFiles;
            $this->existingAttachments = $currentFiles; // Update view
        }

        // Simpan ke DB
        $this->event->update($dataToUpdate);

        // Reset input upload
        $this->reset(['newLetterHeader', 'newAttachments']);

        session()->flash('message', 'Pengaturan Surat & Lampiran berhasil disimpan.');
    }

    public function saveMessageSettings()
    {
        $this->validate([
            'waTemplate' => 'required|string',
            'emailSubject' => 'required|string|max:255',
            'emailBody' => 'required|string',
            'confirmGreeting' => 'nullable|string',
            'invitationEmailBanner' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'waTemplate.required' => 'Template WhatsApp wajib diisi.',
            'emailSubject.required' => 'Subjek email wajib diisi.',
            'emailBody.required' => 'Isi email wajib diisi.',
            'invitationEmailBanner.image' => 'Banner email harus berupa gambar.',
            'invitationEmailBanner.mimes' => 'Format banner email harus jpeg, jpg, atau png.',
            'invitationEmailBanner.max' => 'Ukuran banner email maksimal 2MB.',
        ]);

        $dataToUpdate = [
            'invitation_wa_template' => $this->waTemplate,
            'invitation_email_subject' => $this->emailSubject,
            'invitation_email_body' => $this->emailBody,
            'invitation_confirm_greeting' => $this->confirmGreeting,
        ];

        if ($this->invitationEmailBanner) {
            if ($this->event->invitation_email_banner) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($this->event->invitation_email_banner);
            }
            $path = $this->invitationEmailBanner->store('invitation-banners', 'public');
            $dataToUpdate['invitation_email_banner'] = $path;
            $this->existingEmailBannerPath = $path;
        }

        $this->event->update($dataToUpdate);

        $this->reset(['invitationEmailBanner']);

        session()->flash('message', 'Template Pesan & Konfirmasi berhasil disimpan.');
    }

    public function confirmDeleteHeader()
    {
        $this->confirmingHeaderDeletion = true; // Buka modal
    }

    public function cancelDeleteHeader()
    {
        $this->confirmingHeaderDeletion = false; // Tutup modal
    }

    public function deleteLetterHeader()
    {
        // Jika ada file di database, hapus fisiknya
        if ($this->event->invitation_letter_header) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($this->event->invitation_letter_header);
        }

        // Update database menjadi null
        $this->event->update(['invitation_letter_header' => null]);

        // Reset properti component
        $this->existingLetterHeader = null;
        $this->newLetterHeader = null;

        // Tutup modal
        $this->confirmingHeaderDeletion = false;

        session()->flash('message', 'Kop surat berhasil dihapus.');
    }


    // Method Hapus Lampiran Tertentu
    public function removeAttachment($index)
    {
        $files = $this->existingAttachments;
        if (isset($files[$index])) {
            // Hapus file fisik (Opsional, agar hemat storage)
            // \Storage::disk('public')->delete($files[$index]); 

            unset($files[$index]);
            $this->existingAttachments = array_values($files); // Re-index array

            $this->event->update(['invitation_files' => $this->existingAttachments]);
            session()->flash('message', 'Lampiran dihapus.');
        }
    }

    public function import()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // Max 10MB
        ]);

        try {
            // 1. Baca file Excel menjadi Array
            $rows = Excel::toArray(new InvitationsImport($this->event->id), $this->file);
            $data = $rows[0] ?? [];

            if (empty($data)) {
                session()->flash('error', 'File Excel kosong atau tidak terbaca.');
                return;
            }

            // 2. Ambil semua email yang sudah ada di event ini
            $existingEmails = Invitation::where('event_id', $this->event->id)
                ->whereNotNull('email')
                ->pluck('email')
                ->map(fn($e) => strtolower(trim($e)))
                ->toArray();

            $this->duplicateEmails = [];
            $this->uniqueRowsToImport = [];
            $processedEmailsInFile = [];

            foreach ($data as $index => $row) {
                // Mapping row data (menyamakan dengan logic InvitationsImport)
                $name = $row['name'] ?? $row['nama'] ?? null;
                if (!$name) continue; // Lewati jika nama kosong

                $email = isset($row['email']) ? strtolower(trim($row['email'])) : null;
                $phone = $row['phone'] ?? $row['phone_number'] ?? $row['wa'] ?? null;
                $company = $row['company'] ?? $row['instansi'] ?? null;
                $category = $row['jabatan'] ?? $row['category'] ?? $row['kategori'] ?? null;

                $isDuplicate = false;

                if ($email) {
                    // Cek apakah sudah ada di DB atau sudah ada di dalam file itu sendiri
                    if (in_array($email, $existingEmails) || in_array($email, $processedEmailsInFile)) {
                        $isDuplicate = true;
                        $this->duplicateEmails[] = [
                            'row' => $index + 2, // +2 karena heading row dan 0-index
                            'name' => $name,
                            'email' => $email
                        ];
                    } else {
                        $processedEmailsInFile[] = $email;
                    }
                }

                if (!$isDuplicate) {
                    $this->uniqueRowsToImport[] = [
                        'uuid' => (string) \Illuminate\Support\Str::uuid(),
                        'event_id' => $this->event->id,
                        'name' => $name,
                        'email' => $email,
                        'phone_number' => $phone,
                        'company' => $company,
                        'category' => $category ?? 'Uncategorized',
                        'status' => 'pending',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            // 3. Jika ada duplikat, tampilkan modal konfirmasi
            if (!empty($this->duplicateEmails)) {
                $this->showDuplicateModal = true;
            } else {
                // Jika tidak ada duplikat, langsung import
                $this->confirmImportWithoutDuplicates();
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memproses file: ' . $e->getMessage());
        }
    }

    public function confirmImportWithoutDuplicates()
    {
        if (!empty($this->uniqueRowsToImport)) {
            // Insert data secara batch
            foreach (array_chunk($this->uniqueRowsToImport, 100) as $chunk) {
                Invitation::insert($chunk);
            }
            
            $count = count($this->uniqueRowsToImport);
            session()->flash('message', "Berhasil mengimport $count tamu (duplikat dilewati).");
        } else {
            session()->flash('error', 'Tidak ada data baru yang bisa diimport.');
        }

        $this->reset(['file', 'duplicateEmails', 'uniqueRowsToImport', 'showDuplicateModal']);
    }

    public function cancelImport()
    {
        $this->reset(['file', 'duplicateEmails', 'uniqueRowsToImport', 'showDuplicateModal']);
    }

    public function sendEmail($invitationId)
    {
        $invitation = Invitation::find($invitationId);
        if (!$invitation || !$invitation->email) return;

        $parser = app(\App\Services\MessageParserService::class);
        
        // 1. Process Subject
        $processedSubject = str_replace(
            ['{name}', '{event_name}', '{company}'],
            [$invitation->name, $this->event->name, $invitation->company ?? ''],
            $this->emailSubject
        );

        // 2. Process Content (Using central parser for consistent tags)
        $processedBody = $parser->parseInvitation($this->emailBody, $invitation);

        try {
            // 1. First Attempt: Use Organizer's Custom SMTP (if configured)
            Mail::to($invitation->email)->send(new InvitationMail(
                $invitation,
                $processedBody,
                $processedSubject
            ));

            $invitation->update([
                'is_sent_email' => true,
                'email_sent_at' => now(),
            ]);

            $this->dispatch('notify', message: 'Email berhasil terkirim ke ' . $invitation->email);
        } catch (\Exception $e) {
            // 2. Second Attempt (Failover): Use System SMTP if the first one fails
            try {
                $mailable = new InvitationMail($invitation, $processedBody, $processedSubject);
                $mailable->skipOrganizerSmtp = true; // FORCE use system SMTP
                
                Mail::to($invitation->email)->send($mailable);

                $invitation->update([
                    'is_sent_email' => true,
                    'email_sent_at' => now(),
                ]);

                $this->dispatch('notify', message: 'Custom SMTP gagal, tapi email berhasil terkirim via System Fallback.');
            } catch (\Exception $e2) {
                $this->dispatch('notify', message: 'Gagal total mengirim email: ' . $e2->getMessage());
            }
        }
    }

    public function sendWhatsAppDirect($invitationId)
    {
        $invitation = Invitation::find($invitationId);
        if (!$invitation || !$invitation->phone_number) return;

        $template = $this->event->invitation_wa_template;
        if (!$template) {
            $template = "Halo *{name}*, silakan konfirmasi kehadiran Anda di {link_konfirmasi}";
        }

        $parser = app(\App\Services\MessageParserService::class);
        $msg = $parser->parseInvitation($template, $invitation);

        try {
            $whatsapp = new \App\Services\WhatsAppService($this->event->organizer_id);
            $response = $whatsapp->sendMessage($invitation->phone_number, $msg);

            if (isset($response['status']) && $response['status'] == true) {
                $invitation->update([
                    'is_sent_whatsapp' => true,
                    'whatsapp_sent_at' => now(),
                ]);
                $this->dispatch('notify', message: 'WhatsApp API: Berhasil terkirim ke ' . $invitation->name);
            } else {
                $this->dispatch('notify', message: 'WhatsApp API Error: ' . ($response['reason'] ?? 'Gagal terkirim'));
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Error: ' . $e->getMessage());
        }
    }

    public function broadcastWhatsAppWeb($onlyUnsent = true)
    {
        $query = Invitation::where('event_id', $this->event->id);
        
        if ($onlyUnsent) {
            $query->where('is_sent_whatsapp', false);
        }

        $invitations = $query->get();
        $count = 0;
        $errors = 0;

        $whatsapp = new \App\Services\WhatsAppService($this->event->organizer_id);

        $parser = app(\App\Services\MessageParserService::class);

        foreach ($invitations as $invite) {
            if ($invite->phone_number) {
                $template = $this->event->invitation_wa_template ?: "Halo *{name}*, silakan konfirmasi kehadiran Anda di {link_konfirmasi}";
                $msg = $parser->parseInvitation($template, $invite);

                $response = $whatsapp->sendMessage($invite->phone_number, $msg);

                if (isset($response['status']) && $response['status'] == true) {
                    $invite->update([
                        'is_sent_whatsapp' => true,
                        'whatsapp_sent_at' => now(),
                    ]);
                    $count++;
                } else {
                    $errors++;
                }
                
                // Beri jeda 1 detik antar pengiriman agar tidak dianggap spam oleh Fonnte
                sleep(1);
            }
        }

        $this->dispatch('notify', message: "Selesai! $count Berhasil, $errors Gagal.");
    }

    public function markWaSent($invitationId)
    {
        $invitation = Invitation::find($invitationId);
        if ($invitation) {
            $invitation->update([
                'is_sent_whatsapp' => true,
                'whatsapp_sent_at' => now(),
            ]);
            $this->dispatch('notify', message: 'Status WhatsApp diperbarui untuk ' . $invitation->name);
        }
    }

    public function broadcastEmails($onlyUnsent = true)
    {
        $query = Invitation::where('event_id', $this->event->id);
        
        if ($onlyUnsent) {
            $query->where('is_sent_email', false);
        }

        $invitations = $query->get();
        $count = 0;

        foreach ($invitations as $invite) {
            if ($invite->email) {
                SendInvitationJob::dispatch($invite, 'email')->onQueue('broadcast');
                $count++;
            }
        }

        $this->dispatch('notify', message: "Antrean Email siaran dimulai untuk $count tamu.");
    }

    public function broadcastWhatsApp($onlyUnsent = true)
    {
        $query = Invitation::where('event_id', $this->event->id);
        
        if ($onlyUnsent) {
            $query->where('is_sent_whatsapp', false);
        }

        $invitations = $query->get();
        $count = 0;

        foreach ($invitations as $invite) {
            if ($invite->phone_number) {
                SendInvitationJob::dispatch($invite, 'whatsapp')->onQueue('broadcast');
                $count++;
            }
        }

        $this->dispatch('notify', message: "Antrean WhatsApp siaran dimulai untuk $count tamu.");
    }

    public function delete($id)
    {
        Invitation::destroy($id);
    }

    public function downloadTemplate()
    {
        return Excel::download(new InvitationTemplateExport, 'template_undangan.xlsx');
    }

    public function render()
    {
        $invitations = Invitation::with(['submission'])->where('event_id', $this->event->id)
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('company', 'like', '%' . $this->search . '%');
            })
            ->when($this->filterStatus !== 'all', function ($q) {
                $q->where('status', $this->filterStatus);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.event.invitation-manager', [
            'invitations' => $invitations
        ])->layout('layouts.app');
    }
}
