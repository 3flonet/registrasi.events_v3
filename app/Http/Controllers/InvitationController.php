<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Registration;
use App\Models\EventEmailTemplate;
use App\Mail\DynamicBroadcastMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Services\TenantService;

class InvitationController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }
    /**
     * Menampilkan form konfirmasi kehadiran.
     */
    public function show($invitation)
    {
        $invitation = Invitation::where('uuid', $invitation)->firstOrFail();
        $event = $invitation->event()->withoutGlobalScope('organizer')->firstOrFail();

        $this->tenantService->setOrganizer($event->organizer);
        
        // Jika sudah pernah respon, arahkan ke halaman info
        if ($invitation->status !== 'pending') {
            return view('invitation.already-responded', [
                'invitation' => $invitation,
                'event' => $event
            ]);
        }

        return view('invitation.confirm', [
            'invitation' => $invitation,
            'event' => $event
        ]);
    }

    /**
     * Menampilkan Surat Undangan Digital (E-Letter)
     */
    public function letter($invitation)
    {
        $invitation = Invitation::where('uuid', $invitation)->firstOrFail();
        $event = $invitation->event()->withoutGlobalScope('organizer')->firstOrFail();
        
        $this->tenantService->setOrganizer($event->organizer);
        $content = $event->invitation_letter_body;

        if (empty($content)) {
            return redirect()->route('invitation.confirm', $invitation->uuid);
        }

        $confirmLink = route('invitation.confirm', $invitation->uuid);
        $letterLink = route('invitation.letter', $invitation->uuid);

        // Styling Tombol untuk di dalam surat
        $confirmButton = '<a href="' . $confirmLink . '" style="display:inline-block;background:#4f46e5;color:#ffffff;padding:12px 24px;text-decoration:none;border-radius:10px;font-weight:800;font-size:12px;text-transform:uppercase;letter-spacing:0.1em;box-shadow:0 4px 6px -1px rgba(79, 70, 229, 0.2);margin:10px 0;">Konfirmasi Kehadiran</a>';

        $replacements = [
            '{name}'            => $invitation->name,
            '{company}'         => $invitation->company ?? '',
            '{category}'        => $invitation->category ?? '',
            '{jabatan}'         => $invitation->category ?? '', // Alias untuk konsistensi
            '{event_name}'      => $event->name,
            '{link_surat}'      => $letterLink,    // URL biasa
            '{link_konfirmasi}' => $confirmButton, // Tombol CTA
        ];

        $processedContent = str_replace(array_keys($replacements), array_values($replacements), $content);

        return view('invitation.letter', [
            'invitation' => $invitation,
            'event' => $event,
            'content' => $processedContent,
        ]);
    }

    /**
     * Memproses data konfirmasi.
     */
    public function submit(Request $request, $invitation)
    {
        $invitation = Invitation::where('uuid', $invitation)->firstOrFail();
        $event = $invitation->event()->withoutGlobalScope('organizer')->firstOrFail();

        // Validasi input dasar
        $request->validate([
            'response_status' => 'required|in:confirmed,represented,declined',
        ]);

        // 0. Validasi Sesi/Kelas jika event memiliki sesi
        if ($request->response_status === 'confirmed' || $request->response_status === 'represented') {
            $selectedSessionsInput = $request->input('selected_sessions', []);
            foreach ($event->sessionGroups as $group) {
                $val = $selectedSessionsInput[$group->id] ?? null;
                if ($group->is_required) {
                    if ($group->selection_type === 'multiple') {
                        if (empty($val) || !is_array($val) || count(array_filter($val)) === 0) {
                            return redirect()->back()->withInput()->withErrors(['selected_sessions.' . $group->id => 'Anda wajib memilih minimal satu sesi pada kelompok ' . $group->name]);
                        }
                    } else {
                        if (empty($val)) {
                            return redirect()->back()->withInput()->withErrors(['selected_sessions.' . $group->id => 'Anda wajib memilih sesi pada kelompok ' . $group->name]);
                        }
                    }
                }

                // Cek Kuota Sesi
                $sessionIds = [];
                if (is_array($val)) {
                    $sessionIds = array_filter($val);
                } elseif (!empty($val)) {
                    $sessionIds = [$val];
                }

                foreach ($sessionIds as $sessId) {
                    $session = \App\Models\EventSession::find($sessId);
                    if ($session && $session->quota > -1) {
                        // Jangan hitung registrasi diri sendiri jika sudah terdaftar sebelumnya (update status)
                        $existingReg = Registration::withoutGlobalScope('organizer')
                            ->where('event_id', $invitation->event_id)
                            ->where('email', $request->response_status === 'confirmed' ? $request->email : $request->rep_email)
                            ->first();

                        $query = $session->registrations();
                        if ($existingReg) {
                            $query->where('registration_id', '!=', $existingReg->id);
                        }
                        $currentRegistrants = $query->count();
                        if ($currentRegistrants >= $session->quota) {
                            return redirect()->back()->withInput()->withErrors(['selected_sessions.' . $group->id => 'Maaf, kuota untuk sesi "' . $session->getTranslation('title', app()->getLocale()) . '" sudah penuh.']);
                        }
                    }
                }
            }
        }

        DB::beginTransaction();
        try {
            // 1. Update Status Undangan di tabel 'invitations'
            $invitation->status = $request->response_status;
            $invitation->responded_at = now();

            // Siapkan variabel data calon pendaftar (Default pakai data undangan)
            $regName = $invitation->name;
            $regEmail = $invitation->email;
            $regPhone = $invitation->phone_number;
            $regData = [
                'nama_instansi' => $invitation->company,
                'tipe_instansi' => 'Invited Guest', // Flag khusus
                'jabatan' => $invitation->category ?? 'Guest',
                'source' => 'Invitation System'
            ];
            $attendanceType = $event->type; // Default ikut tipe event

            // 2. Logika Berdasarkan Pilihan User
            if ($request->response_status === 'confirmed') {
                // Validasi jika user mengupdate data diri sendiri
                $request->validate([
                    'name' => 'required|string',
                    'email' => 'required|email',
                    'phone' => 'required|string',
                ]);

                $regName = $request->name;
                $regEmail = $request->email;
                $regPhone = $request->phone;
                $regData['jabatan'] = $request->jabatan ?? $regData['jabatan'];
                $regData['nama_instansi'] = $request->company ?? $regData['nama_instansi'];

                // Jika event Hybrid, user harus pilih online/offline
                if ($event->type === 'hybrid') {
                    $request->validate(['attendance_type' => 'required|in:offline,online']);
                    $attendanceType = $request->attendance_type;
                }
            } elseif ($request->response_status === 'represented') {
                // Validasi data perwakilan
                $request->validate([
                    'rep_name' => 'required|string',
                    'rep_email' => 'required|email',
                    'rep_phone' => 'required|string',
                ]);

                $regName = $request->rep_name;
                $regEmail = $request->rep_email;
                $regPhone = $request->rep_phone;
                $regData['jabatan'] = $request->rep_jabatan ?? 'Representative';
                $regData['nama_instansi'] = $request->rep_company ?? $regData['nama_instansi'];

                $regData['representing'] = $invitation->name;

                // Simpan data wakil di tabel invitation sebagai history
                $invitation->representative_data = [
                    'name' => $regName,
                    'email' => $regEmail,
                    'phone' => $regPhone,
                    'jabatan' => $regData['jabatan']
                ];

                if ($event->type === 'hybrid') {
                    $request->validate(['attendance_type' => 'required|in:offline,online']);
                    $attendanceType = $request->attendance_type;
                }
            } elseif ($request->response_status === 'declined') {
                // Jika menolak, cukup simpan alasan dan selesai
                $invitation->rejection_reason = $request->rejection_reason;
                $invitation->save();
                DB::commit();

                return redirect()->route('home')->with('status', 'Terima kasih atas konfirmasi Anda. Kami menyayangkan ketidakhadiran Anda.');
            }

            // Simpan perubahan pada invitation
            $invitation->save();
            $existingUser = User::where('email', $regEmail)->first();
            $userId = $existingUser ? $existingUser->id : null;

            // 3. Buat data Registration (Agar dapat QR Code & Tiket)
            // Kita pakai try-catch khusus di sini untuk menangani duplikat email di event yg sama
            $registration = Registration::withoutGlobalScope('organizer')->updateOrCreate(
                [
                    'event_id' => $invitation->event_id,
                    'email' => $regEmail, // Unik per event
                ],
                [
                    'name' => $regName,
                    'phone_number' => $regPhone,
                    'attendance_type' => $attendanceType,
                    'data' => $regData,
                    'user_id' => $userId,
                    'organizer_id' => $event->organizer_id, // KRITIKAL: Agar tampil di dashboard organizer
                    'status' => 'confirmed', // Invitations are pre-approved
                    'payment_status' => 'paid',
                ]
            );

            // Sync Selected Sessions
            $sessionIdsToAttach = [];
            $selectedSessionsInput = $request->input('selected_sessions', []);
            foreach ($selectedSessionsInput as $groupId => $val) {
                if (is_array($val)) {
                    $sessionIdsToAttach = array_merge($sessionIdsToAttach, array_filter($val));
                } elseif (!empty($val)) {
                    $sessionIdsToAttach[] = $val;
                }
            }
            $registration->sessions()->sync($sessionIdsToAttach);

            // 4. Proses Custom Form Inquiry (Jika terhubung)
            if ($event->inquiry_form_id && ($request->response_status === 'confirmed' || $request->response_status === 'represented')) {
                $inquiryForm = \App\Models\InquiryForm::find($event->inquiry_form_id);
                if ($inquiryForm) {
                    $customData = $request->input('custom_fields', []);
                    
                    // Validasi Sederhana
                    foreach ($inquiryForm->fields as $field) {
                        if (isset($field['required']) && $field['required']) {
                            $fieldName = $field['name'];
                            $isFileType = in_array($field['type'], ['file', 'image']);
                            
                            if ($isFileType) {
                                if (!$request->hasFile("custom_files.$fieldName") && !$submission->hasMedia('attachments')) {
                                     throw new \Exception("File '" . $field['label'] . "' wajib diunggah.");
                                }
                            } else {
                                if (empty($customData[$fieldName])) {
                                    throw new \Exception("Field '" . $field['label'] . "' wajib diisi.");
                                }
                            }
                        }
                    }

                    // Tambahkan indikator file ke data
                    if ($request->hasFile('custom_files')) {
                        foreach ($request->allFiles()['custom_files'] as $fieldName => $file) {
                            $customData[$fieldName] = '[File Attached]';
                        }
                    }

                    // Buat atau Update Submission
                    $submission = \App\Models\InquirySubmission::updateOrCreate(
                        [
                            'invitation_id' => $invitation->id,
                        ],
                        [
                            'inquiry_form_id' => $inquiryForm->id,
                            'organizer_id'    => $event->organizer_id,
                            'registration_id' => $registration->id,
                            'data'            => $customData,
                            'status'          => 'approved', // Langsung approve karena ini undangan resmi
                        ]
                    );

                    // Handle File Uploads
                    if ($request->hasFile('custom_files')) {
                        foreach ($request->file('custom_files') as $fieldName => $file) {
                            $submission->addMedia($file)
                                ->usingName($fieldName . '_' . $file->getClientOriginalName())
                                ->toMediaCollection('attachments');
                        }
                    }

                    // Handle Base64 Signatures
                    foreach ($inquiryForm->fields as $field) {
                        if ($field['type'] === 'signature' && !empty($customData[$field['name']])) {
                            $base64 = $customData[$field['name']];
                            if (str_starts_with($base64, 'data:image')) {
                                $submission->addMediaFromBase64($base64)
                                    ->usingName('signature_' . $field['name'])
                                    ->usingFileName('signature_' . $field['name'] . '.png')
                                    ->toMediaCollection('attachments');
                                
                                // Bersihkan data base64 dari JSON agar tidak berat di DB
                                $customData[$field['name']] = '[Digital Signature Attached]';
                                $submission->update(['data' => $customData]);
                            }
                        }
                    }
                }
            }

            if ($event->confirmation_template_id) {
                $template = EventEmailTemplate::withoutGlobalScope('organizer')->find($event->confirmation_template_id);
                if ($template) {
                    \Illuminate\Support\Facades\Log::info('Attempting to send invitation confirmation email', [
                        'registration_id' => $registration->id,
                        'email' => $registration->email,
                        'template_id' => $template->id
                    ]);
                    // Kirim Email secara langsung agar user segera menerima tiket dengan Smart Fallback
                    \App\Services\EmailService::send($registration->email, new DynamicBroadcastMail($template, $registration));
                    \Illuminate\Support\Facades\Log::info('Invitation confirmation email sent successfully');

                    // --- TAMBAHAN: KIRIM WHATSAPP TIKET OTOMATIS ---
                    if ($registration->phone_number) {
                        try {
                            $whatsapp = new \App\Services\WhatsAppService($event->organizer_id);
                            $ticketUrl = route('tickets.qrcode', $registration->uuid);
                            $eventDate = \Carbon\Carbon::parse($event->start_date)->translatedFormat('d M Y');
                            $ticketCode = strtoupper(substr($registration->uuid, 0, 8));
                            $organizerName = $event->organizer->name ?? config('app.name');

                            $parser = app(\App\Services\MessageParserService::class);
                            if ($template->whatsapp_content) {
                                $parsedData = $parser->parseForWhatsApp($template->whatsapp_content, $registration, $template);
                                $msg = $parsedData['message'];
                                $attachmentUrl = $parsedData['attachment_url'];
                                $fallbackUrl = $parsedData['fallback_url'];
                            } else {
                                $eventInstruction = match($registration->attendance_type) {
                                    'online'  => "Silakan bergabung secara virtual melalui " . ($event->platform ?? 'tautan') . " berikut: " . ($event->meeting_link ?? '-'),
                                    'offline' => "Silakan datang langsung ke lokasi acara di: " . ($event->venue ?? 'Lokasi segera dikonfirmasi'),
                                    default   => "Detail kehadiran akan dikirimkan melalui email terpisah."
                                };

                                $msg = "🎟️ *Tiket Elektronik Anda Telah Terbit!*\n\n" . 
                                       "Halo *{$registration->name}*, terima kasih telah melakukan konfirmasi kehadiran untuk acara *{$event->name}*. Data Anda telah berhasil kami catat di sistem pendaftaran.\n\n" . 
                                       "Berikut adalah detail jadwal acara Anda:\n📅 Tanggal: *{$eventDate}*\n📍 LOKASI: {$eventInstruction}\n🎫 Tautan Tiket (QR Code):\n{$ticketUrl}\n\n" . 
                                       "Mohon simpan pesan ini dan tunjukkan QR Code tersebut kepada petugas di meja registrasi saat tiba di lokasi acara nanti.\n\n" .
                                       "Sampai jumpa di lokasi, kami menantikan kehadiran Anda!\n\n" . 
                                       "_Note: Jika tautan di atas tidak dapat diklik, mohon simpan nomor ini terlebih dahulu ke dalam daftar kontak Anda._";
                                
                                $attachmentUrl = 'https://quickchart.io/qr?text=' . urlencode(route('checkin.scan', $registration->uuid)) . '&size=250&margin=1';
                                $fallbackUrl = $ticketUrl;
                            }

                            if ($attachmentUrl) {
                                $whatsapp->sendFile($registration->phone_number, $msg, $attachmentUrl, $fallbackUrl, 'QR_CODE_TICKET.png');
                            } else {
                                $whatsapp->sendMessage($registration->phone_number, $msg);
                            }
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::error('WhatsApp Failed (Invitation Confirmation): ' . $e->getMessage());
                        }
                    }
                } else {
                    \Illuminate\Support\Facades\Log::warning('Confirmation template not found for event: ' . $event->id);
                }
            } else {
                \Illuminate\Support\Facades\Log::warning('No confirmation template ID set for event: ' . $event->id);
            }

            DB::commit();

            // 5. Redirect ke Halaman Sukses yang sudah ada (menampilkan QR Code)
            return redirect()->route('events.register.success', [
                'event' => $event->slug,
                'registration' => $registration->uuid
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memproses data: ' . $e->getMessage())->withInput();
        }
    }
}
