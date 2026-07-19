<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use App\View\Composers\MenuComposer;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register TenantService as a singleton
        $this->app->singleton(\App\Services\TenantService::class, function ($app) {
            return new \App\Services\TenantService();
        });

        // LOGIKA PINTAR:
        // Hanya ubah path ke 'public_html' jika di Production (Hosting).
        // Jika di Local (Laptop), biarkan default (folder 'public').
        // if ($this->app->environment('production')) {
        //     $this->app->usePublicPath(base_path('../public_html'));
        // }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        
        
        try {
            Storage::extend('google', function($app, $config) {
                $options = [];

                if (!empty($config['teamDriveId'] ?? null)) {
                    $options['teamDriveId'] = $config['teamDriveId'];
                }

                $client = new \Google\Client();
                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);
                $client->refreshToken($config['refreshToken']);

                $service = new \Google\Service\Drive($client);
                $adapter = new \Masbug\Flysystem\GoogleDriveAdapter($service, $config['folderId'] ?? '/', $options);
                $driver  = new \League\Flysystem\Filesystem($adapter);

                return new \Illuminate\Filesystem\FilesystemAdapter($driver, $adapter);
            });
        } catch(\Exception $e) {
            // Log error jika perlu, atau biarkan kosong agar tidak crash saat config belum lengkap
        }
        
        
        try {
            
            DB::connection()->getPdo();
            
            if (Schema::hasTable('settings')) {

                // 1. Ambil SEMUA settings dari cache, atau dari DB jika cache kosong
                $settings = Cache::rememberForever('app_settings', function () {

                    // 2. Ambil semua, 'keyBy' agar jadi array asosiatif (cth: 'app_name' => 'AIGIS MOI')
                    return Setting::withoutGlobalScopes()->whereNull('organizer_id')->get()->keyBy('key')->map(function ($setting) {
                        return $setting->value;
                    })->toArray();
                });

                // 3. Muat semua settings ke 'config' Laravel
                // Sekarang kamu bisa panggil config('settings.app_name'), config('settings.footer_email'), dll.
                Config::set('settings', $settings);

                // 4. (Opsional) Override config 'app.name' bawaan Laravel
                // Kita ambil dari array $settings yang sudah di-load
                if (isset($settings['app_name'])) {
                    Config::set('app.name', $settings['app_name']);
                }

                // 5. Override Google Drive Config
                if (isset($settings['gdrive_client_id'])) {
                    Config::set('filesystems.disks.google.clientId', $settings['gdrive_client_id']);
                }
                if (isset($settings['gdrive_client_secret'])) {
                    Config::set('filesystems.disks.google.clientSecret', $settings['gdrive_client_secret']);
                }
                if (isset($settings['gdrive_refresh_token'])) {
                    Config::set('filesystems.disks.google.refreshToken', $settings['gdrive_refresh_token']);
                }
                if (isset($settings['gdrive_folder_id'])) {
                    Config::set('filesystems.disks.google.folderId', $settings['gdrive_folder_id']);
                }
                if (isset($settings['gdrive_team_drive_id'])) {
                    Config::set('filesystems.disks.google.teamDriveId', $settings['gdrive_team_drive_id']);
                }

                // 6. Override Midtrans Config
                if (isset($settings['midtrans_server_key'])) {
                    Config::set('midtrans.server_key', $settings['midtrans_server_key']);
                }
                if (isset($settings['midtrans_client_key'])) {
                    Config::set('midtrans.client_key', $settings['midtrans_client_key']);
                }
                if (isset($settings['midtrans_is_production'])) {
                    Config::set('midtrans.is_production', $settings['midtrans_is_production'] == '1');
                }
                if (isset($settings['midtrans_is_sanitized'])) {
                    Config::set('midtrans.is_sanitized', $settings['midtrans_is_sanitized'] == '1');
                }
                if (isset($settings['midtrans_is_3ds'])) {
                    Config::set('midtrans.is_3ds', $settings['midtrans_is_3ds'] == '1');
                }

                // 6.5 Override Global SMTP / Mail Config
                if (!empty($settings['mail_host'])) {
                    Config::set('mail.mailers.smtp.host', $settings['mail_host']);
                }
                if (!empty($settings['mail_port'])) {
                    Config::set('mail.mailers.smtp.port', $settings['mail_port']);
                }
                if (!empty($settings['mail_username'])) {
                    Config::set('mail.mailers.smtp.username', $settings['mail_username']);
                }
                if (!empty($settings['mail_password'])) {
                    Config::set('mail.mailers.smtp.password', $settings['mail_password']);
                }
                if (!empty($settings['mail_encryption'])) {
                    Config::set('mail.mailers.smtp.encryption', $settings['mail_encryption']);
                }
                if (!empty($settings['mail_from_address'])) {
                    Config::set('mail.from.address', $settings['mail_from_address']);
                }
                if (!empty($settings['mail_from_name'])) {
                    Config::set('mail.from.name', $settings['mail_from_name']);
                }

                // 7. Override Pusher / Broadcasting Config
                if (isset($settings['broadcast_driver'])) {
                    Config::set('broadcasting.default', $settings['broadcast_driver']);
                }
                if (isset($settings['pusher_app_id'])) {
                    Config::set('broadcasting.connections.pusher.app_id', $settings['pusher_app_id']);
                }
                if (isset($settings['pusher_app_key'])) {
                    Config::set('broadcasting.connections.pusher.key', $settings['pusher_app_key']);
                }
                if (isset($settings['pusher_app_secret'])) {
                    Config::set('broadcasting.connections.pusher.secret', $settings['pusher_app_secret']);
                }
                if (isset($settings['pusher_app_cluster'])) {
                    Config::set('broadcasting.connections.pusher.options.cluster', $settings['pusher_app_cluster']);
                    
                    // PERBAIKAN: Jika host kosong, susun host berdasarkan cluster yang baru
                    if (empty($settings['pusher_host'])) {
                        Config::set('broadcasting.connections.pusher.options.host', 'api-'.$settings['pusher_app_cluster'].'.pusher.com');
                    }
                }
                if (!empty($settings['pusher_host'])) {
                    Config::set('broadcasting.connections.pusher.options.host', $settings['pusher_host']);
                }
                if (!empty($settings['pusher_port'])) {
                    Config::set('broadcasting.connections.pusher.options.port', $settings['pusher_port']);
                }
                if (!empty($settings['pusher_scheme'])) {
                    Config::set('broadcasting.connections.pusher.options.scheme', $settings['pusher_scheme']);
                }
            }
        } catch (\Exception $e) {
            // Tangani error jika koneksi DB gagal, dll.
            \Log::error('Could not load settings from database: ' . $e->getMessage());
        }

        // Baris ini sudah benar dari kodemu, biarkan saja
        View::composer('livewire.layout.navigation', MenuComposer::class);

        DB::connection()->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
    }
}
