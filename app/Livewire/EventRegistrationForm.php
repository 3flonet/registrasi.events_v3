<?php

namespace App\Livewire;

use App\Models\Event;
use App\Models\User;
use App\Models\Registration;
use App\Notifications\NewRegistrationNotification;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationConfirmationMail;
use App\Mail\InvoiceMail; // <-- TAMBAHAN: Import InvoiceMail
use Illuminate\Support\Facades\Log;


use App\Models\TicketTier;
use App\Models\Voucher;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;



class EventRegistrationForm extends Component
{
    use WithFileUploads;


    public Event $event;
    // Properti untuk field standar
    public $name = '';
    public $email = '';
    public $phone_number = '';
    // Payment Data
    public $selectedTierId;
    public $voucherCode;
    public $voucherApplied = null;
    public $summary = [
        'price' => 0,
        'discount' => 0,
        'total' => 0
    ];
    // Properti untuk field kustom
    public array $formData = [];
    public bool $success = false;
    public array $combinedFields = [];

    public $attendance_type = 'offline'; // Default 'offline', akan relevan jika event hybrid
    public array $selected_sessions = [];

    public function boot(\App\Services\TenantService $tenantService)
    {
        if ($this->event && $this->event->organizer) {
            $tenantService->setOrganizer($this->event->organizer);
        }
    }

    public function mount(\App\Services\TenantService $tenantService)
    {
        $tenantService->setOrganizer($this->event->organizer);
        $this->formData['tipe_instansi'] = '';
        
        // Inisialisasi formData untuk field dari InquiryForm (jika ada)
        if ($this->event->inquiryForm) {
            foreach ($this->event->inquiryForm->fields as $field) {
                $this->formData[$field['name']] = $field['type'] === 'checkbox-multiple' ? [] : '';
            }
        }

        // BARU: Inisialisasi formData untuk field tambahan dari field_config (jika ada)
        if (!empty($this->event->field_config)) {
            foreach ($this->event->field_config as $fieldName => $config) {
                if ($config['active'] ?? false) {
                    $this->formData[$fieldName] = '';
                }
            }
        }

        // Isi otomatis jika user sudah login
        if (auth()->check()) {
            $user = auth()->user();
            $this->name = $user->name;
            $this->email = $user->email;
            $this->phone_number = $user->phone_number;
        }

        if ($this->event->is_paid_event && $this->event->ticketTiers->count() > 0) {
            // Select the first available tier that is not sold out and within sales dates
            $firstAvailable = $this->event->ticketTiers->filter(fn($t) => $t->isAvailable() && !$t->isSoldOut())->first();
            
            // Fallback to first tier if none available (though it will be disabled in UI)
            $this->selectedTierId = $firstAvailable ? $firstAvailable->id : $this->event->ticketTiers->first()->id;
            
            $this->calculateSummary();
        }

        $fieldConfig = $this->event->field_config ?? [];
        $this->combinedFields = [];
        $processedConfigFields = [];

        // Prioritas 1: Proses field tambahan dari field_config
        foreach ($fieldConfig as $name => $config) {
            if ($config['active'] ?? false) {
                $this->formData[$name] = '';

                $type = match ($name) {
                    'tipe_instansi' => 'select',
                    'alamat'        => 'textarea',
                    'tanda_tangan'  => 'signature',
                    default         => 'text',
                };

                $options = [];
                if ($name === 'tipe_instansi' && !empty($config['options'])) {
                    $options = array_map('trim', explode(',', $config['options']));
                }

                // ==========================================================
                // --- INI ADALAH PERBAIKANNYA ---
                // Secara eksplisit tambahkan 'Others' ke dalam array options
                if ($name === 'tipe_instansi') {
                    $options[] = 'Others';
                }
                // ==========================================================

                $translationMap = [
                    'en' => [
                        'nama_instansi' => 'Company Name',
                        'tipe_instansi' => 'Company Type',
                        'jabatan' => 'Job Title',
                        'alamat' => 'Address',
                        'tanda_tangan' => 'Signature',
                    ],
                    'id' => [
                        'nama_instansi' => 'Nama Instansi',
                        'tipe_instansi' => 'Tipe Instansi',
                        'jabatan' => 'Jabatan',
                        'alamat' => 'Alamat',
                        'tanda_tangan' => 'Tanda Tangan',
                    ]
                ];
                $locale = app()->getLocale();
                $label = $translationMap[$locale][$name] ?? Str::title(str_replace('_', ' ', $name));

                $this->combinedFields[] = [
                    'name' => $name,
                    'label' => $label,
                    'type' => $type,
                    'options' => $options,
                    'required' => $config['required'],
                ];
                $processedConfigFields[] = $name;
            }
        }

        // Prioritas 2: Proses field kustom dari InquiryForm (tidak berubah)
        if ($this->event->inquiryForm) {
            foreach ($this->event->inquiryForm->fields as $field) {
                if (!in_array($field['name'], $processedConfigFields)) {
                    $this->formData[$field['name']] = $field['type'] === 'checkbox-multiple' ? [] : '';
                    if (!empty($field['has_others'])) {
                        $this->formData[$field['name'] . '_other'] = '';
                        $field['options'][] = 'Others';
                    }
                    $this->combinedFields[] = $field;
                }
            }
        }

        // Initialize selected_sessions array
        foreach ($this->event->sessionGroups as $group) {
            $this->selected_sessions[$group->id] = $group->selection_type === 'multiple' ? [] : '';
        }
    }

    // Hitung ulang saat tiket berubah
    public function updatedSelectedTierId()
    {
        $this->calculateSummary();
    }

    // Fitur Voucher
    public function applyVoucher()
    {
        $this->validate(['voucherCode' => 'required|string']);

        $voucher = Voucher::where('code', $this->voucherCode)
            ->where('is_active', true)
            ->where('organizer_id', $this->event->organizer_id) // Hard-lock ke organizer pemilik event
            ->where(function($q) {
                // Voucher bisa global (null) ATAU spesifik ke event ini
                $q->whereNull('event_id')->orWhere('event_id', $this->event->id);
            })
            ->first();

        // Validasi Sederhana (Bisa diperkompleks sesuai request sebelumnya)
        if (!$voucher) {
            $this->addError('voucherCode', 'Kode voucher tidak ditemukan.');
            return;
        }

        // Cek Expired & Limit (Panggil helper di Model Voucher Fase 2)
        if (!$voucher->isValidForUser(Auth::id())) {
            $this->addError('voucherCode', 'Voucher tidak valid atau sudah habis.');
            return;
        }

        $this->voucherApplied = $voucher;
        $this->calculateSummary();

        // SweetAlert (lewat browser event)
        $this->dispatch('swal:success', message: 'Voucher berhasil dipasang!');
    }

    public function removeVoucher()
    {
        $this->voucherApplied = null;
        $this->voucherCode = '';
        $this->calculateSummary();
    }

    // Hitung Total Bayar
    public function calculateSummary()
    {
        if (!$this->event->is_paid_event) {
            return;
        }

        $tier = TicketTier::find($this->selectedTierId);
        $price = $tier ? $tier->price : 0;
        $discount = 0;

        if ($this->voucherApplied) {
            if ($this->voucherApplied->type == 'percentage') {
                $discount = ($price * $this->voucherApplied->amount) / 100;
            } else {
                $discount = $this->voucherApplied->amount;
            }
        }

        // Pastikan tidak minus
        $total = max(0, $price - $discount);

        $this->summary = [
            'price' => $price,
            'discount' => $discount,
            'total' => $total
        ];
    }

    private function updateUserProfileFromRegistration(Registration $registration)
    {
        // Cek apakah pendaftaran ini terhubung ke seorang pengguna
        if ($user = $registration->user) {
            // Daftar field yang ingin kita sinkronkan
            $fieldsToSync = ['nama_instansi', 'tipe_instansi', 'jabatan', 'alamat', 'tanda_tangan', 'phone_number'];

            $profileNeedsUpdate = false;

            foreach ($fieldsToSync as $field) {
                // Cek jika field di profil pengguna masih kosong DAN ada data baru dari form
                if (empty($user->{$field}) && !empty($registration->{$field} ?? $registration->data[$field] ?? null)) {

                    // Ambil data baru
                    $newData = $registration->{$field} ?? $registration->data[$field];

                    // Update properti di model User
                    $user->{$field} = $newData;
                    $profileNeedsUpdate = true;
                }
            }

            // Simpan ke database hanya jika ada perubahan
            if ($profileNeedsUpdate) {
                $user->save();
            }
        }
    }

    public function register(TransactionService $transactionService)
    {
        // 1. --- VALIDASI ---
        if ($this->event->quota > 0 && $this->event->remaining_quota <= 0) {
            $this->addError('quota', 'Sorry, the registration quota for this event is full.');
            return;
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('registrations')
                    ->where('event_id', $this->event->id)
                    ->where(function ($query) {
                        // Email dianggap "terpakai" hanya jika statusnya BUKAN cancelled/rejected
                        return $query->whereNotIn('status', ['cancelled', 'rejected', 'expired']);
                    }),
            ],
            'phone_number' => 'nullable|string|max:20',
            'selectedTierId' => $this->event->is_paid_event ? 'required|exists:ticket_tiers,id' : 'nullable',
        ];

        if ($this->event->type === 'hybrid') {
            $rules['attendance_type'] = 'required|in:offline,online';
        }

        $this->validate($rules);

        // --- TICKET AVAILABILITY CHECK ---
        if ($this->event->is_paid_event) {
            $tier = TicketTier::find($this->selectedTierId);
            if (!$tier || !$tier->isAvailable() || $tier->isSoldOut()) {
                $this->addError('selectedTierId', 'Tiket yang Anda pilih sudah tidak tersedia atau sudah habis.');
                return;
            }
        }

        // Validasi Custom Fields
        $customRules = [];
        // ... (Validasi Custom Fields TETAP SAMA) ...
        $fieldConfig = $this->event->field_config ?? [];
        foreach ($fieldConfig as $fieldName => $config) {
            if (isset($config['active']) && $config['active'] && isset($config['required']) && $config['required']) {
                $customRules['formData.' . $fieldName] = 'required';
                if ($fieldName === 'tipe_instansi' && ($this->formData['tipe_instansi'] ?? '') === 'others') {
                    $customRules['formData.tipe_instansi_other'] = 'required';
                }
            }
        }
        if ($this->event->inquiryForm) {
            foreach ($this->event->inquiryForm->fields as $field) {
                if (isset($field['required']) && $field['required']) {
                    $customRules['formData.' . $field['name']] = 'required';
                }
            }
        }
        if (!empty($customRules)) {
            $this->validate($customRules);
        }

        // Validasi Sesi/Kelas
        foreach ($this->event->sessionGroups as $group) {
            $val = $this->selected_sessions[$group->id] ?? null;
            if ($group->is_required) {
                if ($group->selection_type === 'multiple') {
                    if (empty($val) || !is_array($val) || count(array_filter($val)) === 0) {
                        $this->addError('selected_sessions.' . $group->id, 'You must select at least one session in ' . $group->name);
                        return;
                    }
                } else {
                    if (empty($val)) {
                        $this->addError('selected_sessions.' . $group->id, 'You must select a session in ' . $group->name);
                        return;
                    }
                }
            }

            // Validasi kuota sesi yang dipilih
            $sessionIds = [];
            if (is_array($val)) {
                $sessionIds = array_filter($val);
            } elseif (!empty($val)) {
                $sessionIds = [$val];
            }

            foreach ($sessionIds as $sessId) {
                $session = \App\Models\EventSession::find($sessId);
                if ($session && $session->quota > -1) {
                    $currentRegistrants = $session->registrations()->count();
                    if ($currentRegistrants >= $session->quota) {
                        $this->addError('selected_sessions.' . $group->id, 'Sorry, the session "' . $session->getTranslation('title', app()->getLocale()) . '" is full (quota reached).');
                        return;
                    }
                }
            }
        }


        // 2. --- PROSES PENYIMPANAN & PEMBAYARAN ---

        try {
            $result = DB::transaction(function () use ($transactionService) {
                $existingUser = User::where('email', $this->email)->first();
                $userId = $existingUser ? $existingUser->id : (Auth::id() ?? null);

                $registrationData = [
                    'event_id' => $this->event->id,
                    'user_id' => $userId,
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone_number' => $this->phone_number,
                    'data' => $this->formData,
                    'attendance_type' => $this->event->type === 'hybrid' ? $this->attendance_type : $this->event->type,
                    'ticket_tier_id' => $this->selectedTierId,
                    'total_price' => $this->summary['total'] ?? 0,
                    'status' => 'pending',
                    'payment_status' => 'unpaid',
                    'organizer_id' => $this->event->organizer_id, // FORCE ORGANIZER ID
                ];

                $newRegistration = Registration::create($registrationData);

                // Attach Selected Sessions
                $sessionIdsToAttach = [];
                foreach ($this->selected_sessions as $groupId => $val) {
                    if (is_array($val)) {
                        $sessionIdsToAttach = array_merge($sessionIdsToAttach, array_filter($val));
                    } elseif (!empty($val)) {
                        $sessionIdsToAttach[] = $val;
                    }
                }
                $newRegistration->sessions()->attach($sessionIdsToAttach);
                
                // Create Inquiry Submission for consistency in reporting
                if ($this->event->inquiryForm) {
                    $submission = \App\Models\InquirySubmission::create([
                        'inquiry_form_id' => $this->event->inquiryForm->id,
                        'organizer_id'    => $this->event->organizer_id,
                        'registration_id' => $newRegistration->id,
                        'data'            => $this->formData,
                        'status'          => 'approved',
                    ]);

                    // Handle File/Image Uploads for Livewire
                    foreach ($this->combinedFields as $field) {
                        $fieldName = $field['name'];
                        if (in_array($field['type'], ['image', 'file']) && !empty($this->formData[$fieldName])) {
                            $file = $this->formData[$fieldName];
                            // Livewire TemporaryUploadedFile
                            if ($file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                                $submission->addMedia($file->getRealPath())
                                    ->usingName($fieldName . '_' . $file->getClientOriginalName())
                                    ->usingFileName($fieldName . '_' . $file->getClientOriginalName())
                                    ->toMediaCollection('attachments');
                                
                                // Update data to indicate file attached
                                $currentData = $submission->data;
                                $currentData[$fieldName] = '[File Attached]';
                                $submission->update(['data' => $currentData]);
                            }
                        }

                        // Handle Base64 Signatures (if any in formData)
                        if ($field['type'] === 'signature' && !empty($this->formData[$fieldName])) {
                            $base64 = $this->formData[$fieldName];
                            if (is_string($base64) && str_starts_with($base64, 'data:image')) {
                                $submission->addMediaFromBase64($base64)
                                    ->usingName('signature_' . $fieldName)
                                    ->usingFileName('signature_' . $fieldName . '.png')
                                    ->toMediaCollection('attachments');
                                
                                $currentData = $submission->data;
                                $currentData[$fieldName] = '[Digital Signature Attached]';
                                $submission->update(['data' => $currentData]);
                            }
                        }
                    }
                }

                $this->updateUserProfileFromRegistration($newRegistration);

                $snapToken = null;

                if ($this->event->is_paid_event && $this->summary['total'] > 0) {
                    // --- BERBAYAR ---
                    // Kami tidak membuat transaksi di sini lagi.
                    // Transaksi akan dibuat di halaman Invoice setelah user memilih Channel.
                    
                    // Namun kita tetap kirim notifikasi pendaftaran masuk (tanpa info bayar dulu)
                    // Atau kirim instruksi bayar via WhatsApp
                    $invoiceTemplateId = $this->event->invoice_template_id;
                    $invoiceTemplate = $invoiceTemplateId ? \App\Models\EventEmailTemplate::find($invoiceTemplateId) : null;

                    if ($invoiceTemplate) {
                        try {
                            Mail::to($newRegistration->email)->queue(new \App\Mail\DynamicBroadcastMail($invoiceTemplate, $newRegistration));
                        } catch (\Exception $e) {
                            Log::error('Failed to send invoice email: ' . $e->getMessage());
                        }

                        $this->sendWhatsAppNotification($newRegistration, $invoiceTemplate);
                    }
                } else {
                    // --- GRATIS ---
                    \Illuminate\Support\Facades\Log::info('Processing FREE registration for ID: ' . $newRegistration->id);
                    $newRegistration->update(['status' => 'confirmed', 'payment_status' => 'paid']);
                    \Illuminate\Support\Facades\Log::info('Status updated to confirmed for ID: ' . $newRegistration->id);

                    // Kirim Email Tiket
                    try {
                        if ($this->event->confirmation_template_id) {
                            $template = \App\Models\EventEmailTemplate::find($this->event->confirmation_template_id);
                            if ($template) {
                                \Illuminate\Support\Facades\Log::info('Sending ticket email for template ID: ' . $template->id);
                                Mail::to($newRegistration->email)->send(new \App\Mail\DynamicBroadcastMail($template, $newRegistration));
                                \Illuminate\Support\Facades\Log::info('Ticket email sent SUCCESSFULLY for ID: ' . $newRegistration->id);
                            }
                        }
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Email Failed for ID ' . $newRegistration->id . ': ' . $e->getMessage());
                    }

                    $this->sendWhatsAppNotification($newRegistration, $template);
                }

                // --- TRIGGER NOTIFICATION FOR ADMINS & ORGANIZER TEAM ---
                try {
                    // 1. Ambil Admin Global (Super Admin) - Tanpa filter tenant
                    $globalAdmins = User::withoutGlobalScopes()->role(['Super Admin'])->get();
                    
                    // 2. Ambil semua staff/user yang bernaung di bawah Organizer pemilik event ini
                    $organizerUsers = User::withoutGlobalScopes()->where('organizer_id', $this->event->organizer_id)->get();
                    
                    // Gabungkan keduanya agar tidak ada duplikasi
                    $recipients = $globalAdmins->concat($organizerUsers)->unique('id');

                    if ($recipients->count() > 0) {
                        foreach ($recipients as $recipient) {
                            try {
                                $recipient->notify(new NewRegistrationNotification($newRegistration));
                            } catch (\Exception $e) {
                                Log::error("Notifying User {$recipient->id} failed: " . $e->getMessage());
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Notification Failed: ' . $e->getMessage());
                }

                return [
                    'token' => $snapToken,
                    'registration' => $newRegistration
                ];
            });

            $snapToken = $result['token'];
            $registration = $result['registration'];

            // 3. --- RESPONSE KE BROWSER ---

            if ($this->event->is_paid_event && $this->summary['total'] > 0) {
                // KASUS BERBAYAR: Redirect ke Halaman Invoice (Bukan Popup)
                session()->flash('swal:success', 'Pendaftaran berhasil! Silakan pilih metode pembayaran.');
                return redirect()->route('invoice.show', $registration->uuid);
            } else {
                // KASUS GRATIS: Redirect Sukses
                session()->flash('swal:success', 'Pendaftaran berhasil! Tiket telah dikirim ke email Anda.');
                $this->dispatch('registration-success');
                return redirect()->route('events.register.success', [
                    'event' => $this->event->slug,
                    'registration' => $registration->uuid,
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('swal:error', [
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
            Log::error('Registration Error: ' . $e->getMessage());
        }
    }


    protected function sendWhatsAppNotification($registration, $template)
    {
        if (!$registration->phone_number || !$template) return;

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
                // Fallback Fonnte
                if ($template->whatsapp_content) {
                    $waMsg = $parser->parse($template->whatsapp_content, $registration, $template);
                    $whatsapp->sendMessage($registration->phone_number, $waMsg);
                }
            }
        } catch (\Exception $e) {
            Log::error('EventRegistrationForm WhatsApp Dispatch Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.event-registration-form');
    }
}
