<?php

namespace App\Livewire\Admin\Settings;

use Livewire\Component;
use App\Models\Setting;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;


use App\Models\User;      // Pastikan Model di-import
use App\Models\Event;     // Pastikan Model di-import
use App\Models\Banner;    // Pastikan Model di-import



class Index extends Component
{
    use WithFileUploads;

    // Properti untuk setiap pengaturan
    public $appName, $appAuthor, $appAuthorUrl, $appLogo, $appFavicon, $metaTitle, $metaDescription, $metaKeywords, $contactEmail;

    public $mailHost, $mailPort, $mailUsername, $mailPassword, $mailEncryption, $mailFromAddress, $mailFromName;
    public $testEmailRecipient = '';

    // META WHATSAPP CLOUD API SETTINGS
    public $whatsappBusinessToken, $whatsappPhoneNumberId, $whatsappWabaId, $testWaRecipient;
    public $whatsappStatus = null;
    public $isCheckingWhatsapp = false;

    // GOOGLE DRIVE SETTINGS
    public $gdriveClientId, $gdriveClientSecret, $gdriveRefreshToken, $gdriveFolderId, $gdriveTeamDriveId;

    // MIDTRANS SETTINGS
    public $midtransServerKey, $midtransClientKey, $midtransIsProduction, $midtransIsSanitized, $midtransIs3ds;

    // PLATFORM FEE SETTINGS
    public $platformFeeType, $platformFeeValue, $withdrawalFee;
    public $paymentChannels = [];

    // PUSHER SETTINGS
    public $broadcastDriver, $pusherAppId, $pusherAppKey, $pusherAppSecret, $pusherAppCluster, $pusherHost, $pusherPort, $pusherScheme;

    // ProPERTI BARU UNTUK FOOTER (WhatsApp ditambahkan)
    public $footerLogo, $footerDescription, $footerEmail, $footerPhone, $footerWhatsapp, $footerFacebookUrl, $footerInstagramUrl, $footerWikipediaUrl, $footerYoutubeUrl;

    public $newLogo;
    public $newFooterLogo;
    public $newFavicon;

    public $monitoredFolders = [];
    public $availableFolders = [];

    // Daftar semua key yang kita kelola
    private $keys = [
        'app_name',
        'app_author',
        'app_author_url',
        'app_logo',
        'app_favicon',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'contact_email',
        'footer_logo',
        'footer_description',
        'footer_email',
        'footer_phone',
        'footer_whatsapp', // <-- PERUBAHAN DI SINI
        'footer_facebook_url',
        'footer_instagram_url',
        'footer_wikipedia_url',
        'footer_youtube_url',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
        'maintenance_monitored_folders',
        'whatsapp_business_token',
        'whatsapp_phone_number_id',
        'whatsapp_waba_id',
        'gdrive_client_id',
        'gdrive_client_secret',
        'gdrive_refresh_token',
        'gdrive_folder_id',
        'gdrive_team_drive_id',
        'midtrans_server_key',
        'midtrans_client_key',
        'midtrans_is_production',
        'midtrans_is_sanitized',
        'midtrans_is_3ds',
        'broadcast_driver',
        'pusher_app_id',
        'pusher_app_key',
        'pusher_app_secret',
        'pusher_app_cluster',
        'pusher_host',
        'pusher_port',
        'pusher_scheme',
        'platform_fee_type',
        'platform_fee_value',
        'withdrawal_fee',
    ];

    public function mount()
    {
        $this->loadSettings();
        $this->loadAvailableFolders();
    }

    public function loadAvailableFolders()
    {
        try {
            $dirs = Storage::disk('public')->directories();
            // Filter out system folders
            $forbidden = ['livewire-tmp'];
            $this->availableFolders = array_filter($dirs, fn($d) => !in_array($d, $forbidden));
        } catch (\Exception $e) {
            $this->availableFolders = [];
        }
    }

    public function loadSettings()
    {
        // 1. Ambil semua pengaturan dari database yang milik GLOBAL (organizer_id IS NULL)
        $settings = Setting::withoutGlobalScopes()
            ->whereIn('key', $this->keys)
            ->whereNull('organizer_id')
            ->get()
            ->keyBy('key');

        // Fungsi helper untuk mengambil nilai atau default
        $getValue = fn($key, $default = null) => $settings[$key]->value ?? $default;

        // Tetapkan nilai ke properti publik
        $this->appName = $getValue('app_name', config('app.name'));
        $this->appAuthor = $getValue('app_author');
        $this->appAuthorUrl = $getValue('app_author_url');
        $this->appLogo = $getValue('app_logo');
        $this->appFavicon = $getValue('app_favicon');
        $this->metaTitle = $getValue('meta_title');
        $this->metaDescription = $getValue('meta_description');
        $this->metaKeywords = $getValue('meta_keywords');
        $this->contactEmail = $getValue('contact_email');
        $this->testEmailRecipient = auth()->user()->email;
        $this->testWaRecipient = auth()->user()->phone_number;

        // WhatsApp Business API Settings
        $this->whatsappBusinessToken = $getValue('whatsapp_business_token');
        $this->whatsappPhoneNumberId = $getValue('whatsapp_phone_number_id');
        $this->whatsappWabaId = $getValue('whatsapp_waba_id');

        if ($this->whatsappBusinessToken) {
            $this->checkWhatsappStatus();
        }

        // Memuat pengaturan email
        $this->mailHost = $getValue('mail_host', config('mail.mailers.smtp.host'));
        $this->mailPort = $getValue('mail_port', config('mail.mailers.smtp.port'));
        $this->mailUsername = $getValue('mail_username', config('mail.mailers.smtp.username'));
        $this->mailPassword = $getValue('mail_password', config('mail.mailers.smtp.password'));
        $this->mailEncryption = $getValue('mail_encryption', config('mail.mailers.smtp.encryption'));
        $this->mailFromAddress = $getValue('mail_from_address', config('mail.from.address'));
        $this->mailFromName = $getValue('mail_from_name', config('mail.from.name'));

        // MEMUAT PENGATURAN FOOTER
        $this->footerLogo = $getValue('footer_logo');
        $this->footerDescription = $getValue('footer_description');
        $this->footerEmail = $getValue('footer_email');
        $this->footerPhone = $getValue('footer_phone');
        $this->footerWhatsapp = $getValue('footer_whatsapp'); // <-- PERUBAHAN DI SINI
        $this->footerFacebookUrl = $getValue('footer_facebook_url');
        $this->footerInstagramUrl = $getValue('footer_instagram_url');
        $this->footerWikipediaUrl = $getValue('footer_wikipedia_url');
        $this->footerYoutubeUrl = $getValue('footer_youtube_url');

        // MEMUAT MONITORED FOLDERS
        $monitored = $getValue('maintenance_monitored_folders');
        if($monitored) {
            $this->monitoredFolders = json_decode($monitored, true) ?: [];
        } else {
            $this->monitoredFolders = ['logos', 'favicons']; // Default safe
        }

        // GOOGLE DRIVE
        $this->gdriveClientId = $getValue('gdrive_client_id', config('filesystems.disks.google.clientId'));
        $this->gdriveClientSecret = $getValue('gdrive_client_secret', config('filesystems.disks.google.clientSecret'));
        $this->gdriveRefreshToken = $getValue('gdrive_refresh_token', config('filesystems.disks.google.refreshToken'));
        $this->gdriveFolderId = $getValue('gdrive_folder_id', config('filesystems.disks.google.folderId'));
        $this->gdriveTeamDriveId = $getValue('gdrive_team_drive_id', config('filesystems.disks.google.teamDriveId'));

        // MIDTRANS
        $this->midtransServerKey = $getValue('midtrans_server_key', config('midtrans.server_key'));
        $this->midtransClientKey = $getValue('midtrans_client_key', config('midtrans.client_key'));
        $this->midtransIsProduction = $getValue('midtrans_is_production', config('midtrans.is_production')) == '1';
        $this->midtransIsSanitized = $getValue('midtrans_is_sanitized', config('midtrans.is_sanitized', true)) == '1';
        $this->midtransIs3ds = $getValue('midtrans_is_3ds', config('midtrans.is_3ds', true)) == '1';

        // PUSHER
        $this->broadcastDriver = $getValue('broadcast_driver', config('broadcasting.default'));
        $this->pusherAppId = $getValue('pusher_app_id', config('broadcasting.connections.pusher.app_id'));
        $this->pusherAppKey = $getValue('pusher_app_key', config('broadcasting.connections.pusher.key'));
        $this->pusherAppSecret = $getValue('pusher_app_secret', config('broadcasting.connections.pusher.secret'));
        $this->pusherAppCluster = $getValue('pusher_app_cluster', config('broadcasting.connections.pusher.options.cluster'));
        $this->pusherHost = $getValue('pusher_host', config('broadcasting.connections.pusher.options.host'));
        $this->pusherPort = $getValue('pusher_port', config('broadcasting.connections.pusher.options.port'));
        $this->pusherScheme = $getValue('pusher_scheme', config('broadcasting.connections.pusher.options.scheme'));

        // PLATFORM FEE
        $this->platformFeeType = $getValue('platform_fee_type', 'percentage');
        $this->platformFeeValue = $getValue('platform_fee_value', 0);
        $this->withdrawalFee = $getValue('withdrawal_fee', 0);

        // PAYMENT CHANNELS
        $this->paymentChannels = \App\Models\PaymentChannelConfig::all()->toArray();
    }

    public function save()
    {
        // Validasi input
        $this->validate([
            'appName' => 'nullable|string|max:255',
            'appAuthor' => 'nullable|string|max:255',
            'appAuthorUrl' => 'nullable|url',
            'newLogo' => 'nullable|image|max:1024', // max 1MB
            'newFooterLogo' => 'nullable|image|max:1024', // max 1MB
            'newFavicon' => 'nullable|image|mimes:ico,png|max:1024',
            'metaTitle' => 'nullable|string|max:255',
            'metaDescription' => 'nullable|string',
            'metaKeywords' => 'nullable|string',
            'contactEmail' => 'nullable|email',

            'mailHost' => 'nullable|string',
            'mailPort' => 'nullable|integer',
            'mailUsername' => 'nullable|string',
            'mailPassword' => 'nullable|string',
            'mailEncryption' => 'nullable|string|in:tls,ssl,', // Tambahkan string kosong untuk 'None'
            'mailFromAddress' => 'nullable|email',
            'mailFromName' => 'nullable|string',

            // VALIDASI BARU UNTUK FOOTER
            'footerDescription' => 'nullable|string',
            'footerEmail' => 'nullable|email',
            'footerPhone' => 'nullable|string|max:20',
            'footerWhatsapp' => 'nullable|string|max:20', // <-- PERUBAHAN DI SINI
            'footerFacebookUrl' => 'nullable|url',
            'footerInstagramUrl' => 'nullable|url',
            'footerWikipediaUrl' => 'nullable|url',
            'footerYoutubeUrl' => 'nullable|url',

            'whatsappBusinessToken' => 'nullable|string',
            'whatsappPhoneNumberId' => 'nullable|string',
            'whatsappWabaId' => 'nullable|string',

            'gdriveClientId' => 'nullable|string',
            'gdriveClientSecret' => 'nullable|string',
            'gdriveRefreshToken' => 'nullable|string',
            'gdriveFolderId' => 'nullable|string',
            'gdriveTeamDriveId' => 'nullable|string',

            'midtransServerKey' => 'nullable|string',
            'midtransClientKey' => 'nullable|string',
            'midtransIsProduction' => 'boolean',
            'midtransIsSanitized' => 'boolean',
            'midtransIs3ds' => 'boolean',

            'broadcastDriver' => 'nullable|string',
            'pusherAppId' => 'nullable|string',
            'pusherAppKey' => 'nullable|string',
            'pusherAppSecret' => 'nullable|string',
            'pusherAppCluster' => 'nullable|string',
            'pusherHost' => 'nullable|string',
            'pusherPort' => 'nullable|string',
            'pusherScheme' => 'nullable|string',
        ]);

        // Fungsi helper untuk menyimpan pengaturan GLOBAL
        $saveSetting = function ($key, $value) {
            Setting::withoutGlobalScopes()->updateOrCreate(
                ['key' => $key, 'organizer_id' => null], 
                ['value' => $value]
            );
        };

        // Simpan pengaturan teks
        $saveSetting('app_name', $this->appName);
        $saveSetting('app_author', $this->appAuthor);
        $saveSetting('app_author_url', $this->appAuthorUrl);
        $saveSetting('meta_title', $this->metaTitle);
        $saveSetting('meta_description', $this->metaDescription);
        $saveSetting('meta_keywords', $this->metaKeywords);
        $saveSetting('contact_email', $this->contactEmail);

        // Menyimpan pengaturan email
        $saveSetting('mail_host', $this->mailHost);
        $saveSetting('mail_port', $this->mailPort);
        $saveSetting('mail_username', $this->mailUsername);
        $saveSetting('mail_password', $this->mailPassword);
        $saveSetting('mail_encryption', $this->mailEncryption);
        $saveSetting('mail_from_address', $this->mailFromAddress);
        $saveSetting('mail_from_name', $this->mailFromName);

        // MENYIMPAN PENGATURAN FOOTER
        $saveSetting('footer_description', $this->footerDescription);
        $saveSetting('footer_email', $this->footerEmail);
        $saveSetting('footer_phone', $this->footerPhone);
        $saveSetting('footer_whatsapp', $this->footerWhatsapp); // <-- PERUBAHAN DI SINI
        $saveSetting('footer_facebook_url', $this->footerFacebookUrl);
        $saveSetting('footer_instagram_url', $this->footerInstagramUrl);
        $saveSetting('footer_wikipedia_url', $this->footerWikipediaUrl);
        $saveSetting('footer_youtube_url', $this->footerYoutubeUrl);

        // WHATSAPP BUSINESS API
        $saveSetting('whatsapp_business_token', $this->whatsappBusinessToken);
        $saveSetting('whatsapp_phone_number_id', $this->whatsappPhoneNumberId);
        $saveSetting('whatsapp_waba_id', $this->whatsappWabaId);

        // GOOGLE DRIVE
        $saveSetting('gdrive_client_id', $this->gdriveClientId);
        $saveSetting('gdrive_client_secret', $this->gdriveClientSecret);
        $saveSetting('gdrive_refresh_token', $this->gdriveRefreshToken);
        $saveSetting('gdrive_folder_id', $this->gdriveFolderId);
        $saveSetting('gdrive_team_drive_id', $this->gdriveTeamDriveId);

        // MIDTRANS
        $saveSetting('midtrans_server_key', $this->midtransServerKey);
        $saveSetting('midtrans_client_key', $this->midtransClientKey);
        $saveSetting('midtrans_is_production', $this->midtransIsProduction ? '1' : '0');
        $saveSetting('midtrans_is_sanitized', $this->midtransIsSanitized ? '1' : '0');
        $saveSetting('midtrans_is_3ds', $this->midtransIs3ds ? '1' : '0');

        // PUSHER
        $saveSetting('broadcast_driver', $this->broadcastDriver);
        $saveSetting('pusher_app_id', $this->pusherAppId);
        $saveSetting('pusher_app_key', $this->pusherAppKey);
        $saveSetting('pusher_app_secret', $this->pusherAppSecret);
        $saveSetting('pusher_app_cluster', $this->pusherAppCluster);
        $saveSetting('pusher_host', $this->pusherHost);
        $saveSetting('pusher_port', $this->pusherPort);
        $saveSetting('pusher_scheme', $this->pusherScheme);

        // PLATFORM FEE
        $saveSetting('platform_fee_type', $this->platformFeeType);
        $saveSetting('platform_fee_value', $this->platformFeeValue);
        $saveSetting('withdrawal_fee', $this->withdrawalFee);

        // SAVE PAYMENT CHANNELS
        foreach ($this->paymentChannels as $channel) {
            \App\Models\PaymentChannelConfig::where('id', $channel['id'])->update([
                'fee_type' => $channel['fee_type'],
                'fee_value' => $channel['fee_value'],
                'is_active' => $channel['is_active'] ?? true,
            ]);
        }

        // MENYIMPAN MONITORED FOLDERS
        $saveSetting('maintenance_monitored_folders', json_encode(array_values($this->monitoredFolders)));

        // Proses upload logo jika ada file baru
        if ($this->newLogo) {
            $logoPath = $this->newLogo->store('logos', 'public');
            $saveSetting('app_logo', $logoPath);
        }

        if ($this->newFooterLogo) {
            $footerlogoPath = $this->newFooterLogo->store('logos', 'public');
            $saveSetting('footer_logo', $footerlogoPath);
        }

        // Proses upload favicon jika ada file baru
        if ($this->newFavicon) {
            $faviconPath = $this->newFavicon->store('favicons', 'public');
            $saveSetting('app_favicon', $faviconPath);
        }

        // Membersihkan cache setelah pengaturan diperbarui
        Artisan::call('cache:clear');

        // Beri pesan sukses
        session()->flash('message', 'Settings saved successfully.');
        $this->dispatch('saved');

        // Muat ulang pengaturan untuk menampilkan path file yang baru
        $this->loadSettings();
        $this->loadAvailableFolders();

        // Reset file input
        $this->newLogo = null;
        $this->newFooterLogo = null;
        $this->newFavicon = null;
    }

    public function sendTestEmail()
    {
        $this->validate([
            'testEmailRecipient' => 'required|email',
        ]);

        try {
            \Illuminate\Support\Facades\Mail::purge('smtp');

            Config::set('mail.default', 'smtp');
            Config::set('mail.mailers.smtp.transport', 'smtp');
            Config::set('mail.mailers.smtp.host', $this->mailHost);
            Config::set('mail.mailers.smtp.port', $this->mailPort);
            Config::set('mail.mailers.smtp.encryption', $this->mailEncryption);
            Config::set('mail.mailers.smtp.username', $this->mailUsername);
            Config::set('mail.mailers.smtp.password', $this->mailPassword);
            Config::set('mail.from.address', $this->mailFromAddress);
            Config::set('mail.from.name', $this->mailFromName);
            
            // Correct SSL config for local environments
            Config::set('mail.mailers.smtp.stream', [
                'ssl' => [
                    'allow_self_signed' => true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ]);

            $recipient = $this->testEmailRecipient;

            Mail::raw('Hi! This is a test email to verify your SMTP setup for ' . config('app.name'), function ($message) use ($recipient) {
                $message->to($recipient)->subject('SMTP System Verification');
            });

            session()->flash('mail_success', 'Test email successfully sent to ' . $recipient);
            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'SMTP SUCCESS!',
                'text' => 'The system successfully connected and sent a test email to ' . $recipient,
            ]);

        } catch (\Exception $e) {
            session()->flash('mail_error', 'SMTP ERROR: ' . $e->getMessage());
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'SMTP FAILED',
                'text' => $e->getMessage(),
            ]);
        }
    }

    public function sendTestWhatsApp()
    {
        $this->validate([
            'testWaRecipient' => 'required|string',
            'whatsappBusinessToken' => 'required|string',
            'whatsappPhoneNumberId' => 'required|string',
        ]);

        try {
            // Apply temporary config for testing
            Config::set('settings.whatsapp_business_token', $this->whatsappBusinessToken);
            Config::set('settings.whatsapp_phone_number_id', $this->whatsappPhoneNumberId);
            Config::set('settings.whatsapp_waba_id', $this->whatsappWabaId);

            $whatsapp = app(\App\Services\WhatsAppService::class);
            
            // To test, we send the default Meta template "hello_world" which is guaranteed to exist on all WABA accounts
            $payloadParams = [];

            $result = $whatsapp->sendTemplateMessage(
                $this->testWaRecipient,
                'hello_world',
                'en_US',
                $payloadParams
            );

            if (isset($result['status']) && $result['status'] == true) {
                session()->flash('wa_success', 'Test WhatsApp message (Template: hello_world) sent successfully to ' . $this->testWaRecipient);
            } else {
                session()->flash('wa_error', 'WhatsApp API Error: ' . ($result['reason'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            session()->flash('wa_error', 'Failed to send WA. Error: ' . $e->getMessage());
        }
    }

    public function checkWhatsappStatus()
    {
        $this->isCheckingWhatsapp = true;
        
        try {
            // Apply temporary config for testing
            Config::set('settings.whatsapp_business_token', $this->whatsappBusinessToken);
            Config::set('settings.whatsapp_phone_number_id', $this->whatsappPhoneNumberId);
            Config::set('settings.whatsapp_waba_id', $this->whatsappWabaId);

            $whatsapp = app(\App\Services\WhatsAppService::class);
            $this->whatsappStatus = $whatsapp->getDeviceStatus();
        } catch (\Exception $e) {
            $this->whatsappStatus = [
                'connected' => false,
                'status_text' => 'Error: ' . $e->getMessage()
            ];
        }
        
        $this->isCheckingWhatsapp = false;
    }

    public function clearCache()
    {
        try {
            Artisan::call('view:clear');

            // 1. Bersihkan Cache System
            Artisan::call('optimize:clear');

            // 2. Bersihkan File Upload Sementara (Livewire tmp)
            $tmpPurgeCount = $this->forcePurgeTemporaryUploads();
            
            // Reset komponen upload
            $this->reset(['newLogo', 'newFooterLogo', 'newFavicon']);

            // 3. Hapus File "Yatim Piatu"
            $orphanCleanup = $this->cleanupOrphanedFiles();

            // 4. Hapus Folder Kosong
            $this->cleanupPublicTempFiles();
            $folderCleanupCount = $this->cleanupPublicEmptyFolders();

            $totalFiles = $tmpPurgeCount + $orphanCleanup['success'];
            $failedFiles = $orphanCleanup['failed'];

            $message = "Sistem berhasil disegarkan! <br><br>";
            $message .= "🗑️ <b>{$totalFiles}</b> file sampah telah dihapus. <br>";
            $message .= "📁 <b>{$folderCleanupCount}</b> folder kosong telah dibersihkan.";
            
            if ($failedFiles > 0) {
                $message .= "<br>⚠️ <b>{$failedFiles}</b> file gagal dihapus (mungkin sedang digunakan).";
            }

            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'SYSTEM REFRESHED',
                'html' => $message,
            ]);

            session()->flash('cache_success', "Cache bersih! {$totalFiles} file & {$folderCleanupCount} folder dihapus.");
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'REFRESH FAILED',
                'text' => $e->getMessage(),
            ]);
            session()->flash('cache_error', 'Gagal: ' . $e->getMessage());
        }
    }


    private function cleanupOrphanedFiles()
    {
        $disk = Storage::disk('public');
        $validFiles = [];

        // --- 1. WHITELIST DARI TABEL USERS (Logo, Dokumen, Tanda Tangan) ---
        $users = \Illuminate\Support\Facades\DB::table('users')->get(['logo_path', 'document_path', 'tanda_tangan']);
        foreach ($users as $user) {
            if ($user->logo_path) $validFiles[] = $user->logo_path;
            if ($user->document_path) $validFiles[] = $user->document_path;
            if ($user->tanda_tangan) $validFiles[] = $user->tanda_tangan;
        }

        // --- 2. WHITELIST DARI TABEL TEMPLATE (Banner) ---
        $evtTemplates = \Illuminate\Support\Facades\DB::table('event_email_templates')->pluck('banner_path')->toArray();
        $bcTemplates = \Illuminate\Support\Facades\DB::table('broadcast_templates')->pluck('banner_path')->toArray();
        $evtProgrammes = \Illuminate\Support\Facades\DB::table('event_programmes')->pluck('banner_path')->toArray();
        $validFiles = array_merge($validFiles, $evtTemplates, $bcTemplates, $evtProgrammes);

        $posts = \Illuminate\Support\Facades\DB::table('posts')->pluck('media_url')->toArray();
        $validFiles = array_merge($validFiles, $posts);

        // --- 4. WHITELIST DARI TABEL PRODUCTS & OTHER CORE MODELS ---
        $productImages = \Illuminate\Support\Facades\DB::table('products')->pluck('image_path')->toArray();
        $validFiles = array_merge($validFiles, $productImages);

        // --- 5. PARSE DYNAMIC JSON DATA (Registrations & Inquiries) ---
        // Kadang file diupload melalui form dinamis dan path-nya ada di dalam JSON
        $regData = \Illuminate\Support\Facades\DB::table('registrations')->pluck('data')->toArray();
        $inqData = \Illuminate\Support\Facades\DB::table('inquiry_submissions')->pluck('data')->toArray();
        $allJsonData = array_merge($regData, $inqData);

        foreach ($allJsonData as $json) {
            if (empty($json)) continue;
            $decoded = is_array($json) ? $json : json_decode($json, true);
            if (is_array($decoded)) {
                // Cari string yang menyerupai path file (punya folder/ dan ekstensi)
                array_walk_recursive($decoded, function($val) use (&$validFiles) {
                    if (is_string($val) && str_contains($val, '/') && (str_contains($val, '.') || strlen($val) > 20)) {
                        $validFiles[] = $val;
                    }
                });
            }
        }
        
        // Ambil banner_path dari tabel event_agendas agar tidak terhapus
        $agendas = \Illuminate\Support\Facades\DB::table('event_agendas')->pluck('banner_path')->toArray();
        $validFiles = array_merge($validFiles, $agendas);

        // --- 4. [PENTING] WHITELIST DARI TABEL EVENTS & ORGANIZERS ---
        $orgs = \Illuminate\Support\Facades\DB::table('organizers')->get(['logo_path', 'favicon_path']);
        foreach ($orgs as $org) {
            if ($org->logo_path) $validFiles[] = $org->logo_path;
            if ($org->favicon_path) $validFiles[] = $org->favicon_path;
        }

        $events = \Illuminate\Support\Facades\DB::table('events')->get(['personnel', 'sponsors', 'certificate_config', 'invitation_email_banner', 'invitation_files']);

        foreach ($events as $event) {
            if ($event->invitation_email_banner) $validFiles[] = $event->invitation_email_banner;
            if ($event->invitation_files) {
                $files = json_decode($event->invitation_files, true);
                if (is_array($files)) {
                    foreach ($files as $file) {
                        if (!empty($file['path'])) $validFiles[] = $file['path'];
                        if (!empty($file['url'])) $validFiles[] = $file['url'];
                    }
                }
            }

            // A. Parse Personnel (Speakers & Moderators)
            // Struktur: {"speakers": [{"photo_url": "..."}], "moderators": [...]}
            if (!empty($event->personnel)) {
                $personnelData = json_decode($event->personnel, true);
                if (is_array($personnelData)) {
                    foreach ($personnelData as $group) { // Loop speakers, moderators
                        if (is_array($group)) {
                            foreach ($group as $person) {
                                if (!empty($person['photo_url'])) {
                                    $validFiles[] = $person['photo_url'];
                                }
                            }
                        }
                    }
                }
            }

            // B. Parse Sponsors
            // Struktur: {"platinum": [{"logo_url": "..."}], ...}
            if (!empty($event->sponsors)) {
                $sponsorsData = json_decode($event->sponsors, true);
                if (is_array($sponsorsData)) {
                    foreach ($sponsorsData as $tier) { // Loop platinum, gold, etc
                        if (is_array($tier)) {
                            foreach ($tier as $sponsor) {
                                if (!empty($sponsor['logo_url'])) {
                                    $validFiles[] = $sponsor['logo_url'];
                                }
                            }
                        }
                    }
                }
            }

            // C. Parse Certificate Config
            // Struktur: {"bg_path": "...", "signature_path": "..."}
            if (!empty($event->certificate_config)) {
                $certData = json_decode($event->certificate_config, true);
                if (is_array($certData)) {
                    if (!empty($certData['bg_path'])) {
                        $validFiles[] = $certData['bg_path'];
                    }
                    if (!empty($certData['signature_path'])) {
                        $validFiles[] = $certData['signature_path'];
                    }
                }
            }
        }

        // --- 5. WHITELIST DARI SETTINGS ---
        $settings = \Illuminate\Support\Facades\DB::table('settings')->pluck('value')->toArray();
        foreach ($settings as $val) {
            if (is_string($val) && (str_contains($val, '.') || str_contains($val, '/'))) {
                $validFiles[] = $val;
            }
        }

        // --- 6. NORMALISASI PATH ---
        // Ubah URL atau Path mentah menjadi Path Disk murni (tanpa /storage/ di depan)
        $validFiles = array_map(function ($path) {
            if (empty($path)) return '';
            
            // 1. Jika URL Lengkap (http://...), ambil path-nya saja
            if (str_starts_with($path, 'http')) {
                $parsed = parse_url($path, PHP_URL_PATH);
                $path = $parsed ?: $path;
            }

            // 2. Normalisasi Slash (Sangat penting di Windows)
            $path = str_replace('\\', '/', $path);
            
            // 3. Buang prefiks yang tidak diinginkan
            $prefixes = ['/storage/', 'storage/', '/public/', 'public/'];
            foreach ($prefixes as $prefix) {
                if (str_starts_with($path, $prefix)) {
                    $path = substr($path, strlen($prefix));
                }
            }

            return ltrim($path, '/');
        }, $validFiles);

        // Hapus duplikat dan nilai kosong
        $validFiles = array_unique(array_filter($validFiles));


        // --- 8. [VITAL] WHITELIST DARI SPATIE MEDIA LIBRARY ---
        // Media Library menyimpan file di {id}/{file_name}
        $mediaEntries = \Illuminate\Support\Facades\DB::table('media')->get(['id', 'file_name']);
        foreach ($mediaEntries as $media) {
            $basePath = "{$media->id}";
            $validFiles[] = "{$basePath}/{$media->file_name}";
            
            // Whitelist juga folder konversi (karena Spatie menyimpan thumbnail di sana)
            // Kita tambahkan pattern prefix-nya
            $validFiles[] = "{$basePath}/conversions";
        }


        // --- 8. EKSEKUSI PEMBERSIHAN TERARAH ---
        $deletedCount = 0;
        $failedCount = 0;
        
        // Pastikan kita hanya memindai folder yang dipantau
        $foldersToScan = array_filter($this->monitoredFolders);

        foreach ($foldersToScan as $folder) {
            if ($folder === 'livewire-tmp') continue;
            
            if ($disk->exists($folder)) {
                $files = $disk->allFiles($folder);
                foreach ($files as $file) {
                    if ($file === '.gitignore' || str_contains($file, 'default')) continue;

                    // Normalisasi slash file fisik agar cocok dengan whitelist
                    $normalizedFile = str_replace('\\', '/', $file);

                    // Cek apakah file ada di whitelist yang sudah kita kumpulkan
                    $isWhitelisted = false;
                    foreach ($validFiles as $valid) {
                        if (empty($valid)) continue;
                        // Match exact file or if the file is inside the whitelisted directory
                        if ($normalizedFile === $valid || str_starts_with($normalizedFile, $valid . '/')) {
                            $isWhitelisted = true;
                            break;
                        }
                    }

                    if (!$isWhitelisted) {
                        try {
                            // SAFETY CATCH: Jangan hapus file yang baru diupload (kurang dari 5 menit)
                            // Ini mencegah race condition saat user sedang mengisi form
                            $lastModified = $disk->lastModified($file);
                            if (time() - $lastModified < 300) {
                                continue;
                            }

                            $disk->delete($file);
                            $deletedCount++;
                        } catch (\Exception $e) {
                            $failedCount++;
                        }
                    }
                }
            }
        }

        return [
            'success' => $deletedCount,
            'failed' => $failedCount
        ];
    }


    private function forcePurgeTemporaryUploads()
    {
        $count = 0;
        $disk = Storage::disk('public');
        if ($disk->exists('livewire-tmp')) {
            $tmpFiles = $disk->allFiles('livewire-tmp');
            
            foreach ($tmpFiles as $tmpFile) {
                if ($tmpFile === 'livewire-tmp/.gitignore') continue;
                
                // Hapus semua file sementara secara instan saat sistem di-purge
                try {
                    $disk->delete($tmpFile);
                    $count++;
                } catch (\Exception $e) {
                    // Silent fail
                }
            }
        }
        return $count;
    }

    private function cleanupPublicTempFiles()
    {
        // Pastikan folder ada
        $directory = storage_path('app/public');
        if (!File::isDirectory($directory)) return;

        $files = File::files($directory);

        // Ambil waktu 24 jam yang lalu
        $timestamp = now()->subHours(24)->getTimestamp();

        foreach ($files as $file) {
            // Jangan hapus .gitignore
            if ($file->getFilename() === '.gitignore') continue;

            // Hapus file jika umurnya lebih dari 24 jam
            if ($file->getMTime() < $timestamp) {
                try {
                    File::delete($file->getPathname());
                } catch (\Exception $e) {
                    // Silent fail (abaikan jika gagal hapus)
                }
            }
        }
    }

    private function cleanupPublicEmptyFolders()
    {
        $targetDir = storage_path('app/public');
        if (!File::isDirectory($targetDir)) return 0;
        return $this->deleteEmptySubFolders($targetDir);
    }

    private function deleteEmptySubFolders($path)
    {
        if (!is_dir($path)) return 0;
        
        $deletedCount = 0;
        $items = scandir($path);
        
        foreach ($items as $item) {
            if ($item != '.' && $item != '..') {
                $fullPath = $path . DIRECTORY_SEPARATOR . $item;
                if (is_dir($fullPath)) {
                    $deletedCount += $this->deleteEmptySubFolders($fullPath);
                }
            }
        }
        
        $remainingItems = array_diff(scandir($path), ['.', '..', '.gitignore']);
        
        if (count($remainingItems) === 0 && $path !== storage_path('app/public')) {
            try {
                rmdir($path);
                $deletedCount++;
            } catch (\Exception $e) {
                // Folder maybe not empty or locked
            }
        }
        
        return $deletedCount;
    }

    public function render()
    {
        return view('livewire.admin.settings.index')
            ->layout('layouts.app'); // Sesuaikan dengan layout admin Anda
    }
}
