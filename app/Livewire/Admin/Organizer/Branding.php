<?php

namespace App\Livewire\Admin\Organizer;

use App\Models\Organizer;
use App\Services\TenantService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Branding extends Component
{
    use WithFileUploads;

    public ?Organizer $organizer = null;

    public $name;
    public $description;
    public $logo;
    public $favicon;
    
    public $newLogo;
    public $newFavicon;

    // Midtrans Keys
    public $midtrans_merchant_id;
    public $midtrans_client_key;
    public $midtrans_server_key;

    // SMTP Config
    public $mail_host;
    public $mail_port;
    public $mail_username;
    public $mail_password;
    public $mail_encryption;
    public $mail_from_address;
    public $mail_from_name;
    
    // Testing properties
    public $test_email;

    public function mount(TenantService $tenantService)
    {
        // Redirect Super Admin, as branding is specific to organizers
        if (auth()->user()->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $this->organizer = $tenantService->getOrganizer();

        if (!$this->organizer) {
            return redirect()->route('admin.dashboard')->with('error', 'Organizer context not found.');
        }

        $this->name = $this->organizer->name;
        $this->description = $this->organizer->description;
        $this->logo = $this->organizer->logo_path;
        $this->favicon = $this->organizer->favicon_path;

        // Load Midtrans
        $this->midtrans_merchant_id = \App\Models\Setting::withoutGlobalScopes()->where('organizer_id', $this->organizer->id)->where('key', 'midtrans_merchant_id')->first()?->value;
        $this->midtrans_client_key = \App\Models\Setting::withoutGlobalScopes()->where('organizer_id', $this->organizer->id)->where('key', 'midtrans_client_key')->first()?->value;
        $this->midtrans_server_key = \App\Models\Setting::withoutGlobalScopes()->where('organizer_id', $this->organizer->id)->where('key', 'midtrans_server_key')->first()?->value;

        // Load SMTP
        $this->mail_host = \App\Models\Setting::withoutGlobalScopes()->where('organizer_id', $this->organizer->id)->where('key', 'mail_host')->first()?->value;
        $this->mail_port = \App\Models\Setting::withoutGlobalScopes()->where('organizer_id', $this->organizer->id)->where('key', 'mail_port')->first()?->value;
        $this->mail_username = \App\Models\Setting::withoutGlobalScopes()->where('organizer_id', $this->organizer->id)->where('key', 'mail_username')->first()?->value;
        $this->mail_password = \App\Models\Setting::withoutGlobalScopes()->where('organizer_id', $this->organizer->id)->where('key', 'mail_password')->first()?->value;
        $this->mail_encryption = \App\Models\Setting::withoutGlobalScopes()->where('organizer_id', $this->organizer->id)->where('key', 'mail_encryption')->first()?->value;
        $this->mail_from_address = \App\Models\Setting::withoutGlobalScopes()->where('organizer_id', $this->organizer->id)->where('key', 'mail_from_address')->first()?->value;
        $this->mail_from_name = \App\Models\Setting::withoutGlobalScopes()->where('organizer_id', $this->organizer->id)->where('key', 'mail_from_name')->first()?->value;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'newLogo' => 'nullable|image|max:2048',
            'newFavicon' => 'nullable|image|max:1024',
            'midtrans_merchant_id' => 'nullable|string|max:255',
            'midtrans_client_key' => 'nullable|string|max:255',
            'midtrans_server_key' => 'nullable|string|max:255',
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|numeric',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|string|max:10',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name' => 'nullable|string|max:255',
        ]);

        $data = [
            'name' => $this->name,
            'description' => $this->description,
        ];

        if ($this->newLogo) {
            if ($this->organizer->logo_path) {
                Storage::disk('public')->delete($this->organizer->logo_path);
            }
            $data['logo_path'] = $this->newLogo->store('organizers/logos', 'public');
        }

        if ($this->newFavicon) {
            if ($this->organizer->favicon_path) {
                Storage::disk('public')->delete($this->organizer->favicon_path);
            }
            $data['favicon_path'] = $this->newFavicon->store('organizers/favicons', 'public');
        }

        $this->organizer->update($data);
        $this->organizer->refresh(); // Refresh to get latest paths

        // Simpan Bulk Settings
        $settings = [
            'midtrans_merchant_id' => $this->midtrans_merchant_id,
            'midtrans_client_key' => $this->midtrans_client_key,
            'midtrans_server_key' => $this->midtrans_server_key,
            'mail_host' => $this->mail_host,
            'mail_port' => $this->mail_port,
            'mail_username' => $this->mail_username,
            'mail_password' => $this->mail_password,
            'mail_encryption' => $this->mail_encryption,
            'mail_from_address' => $this->mail_from_address,
            'mail_from_name' => $this->mail_from_name,
        ];

        foreach ($settings as $key => $value) {
            \App\Models\Setting::withoutGlobalScopes()->updateOrCreate(
                ['key' => $key, 'organizer_id' => $this->organizer->id],
                ['value' => $value]
            );
        }

        $this->logo = $this->organizer->logo_path;
        $this->favicon = $this->organizer->favicon_path;
        $this->newLogo = null;
        $this->newFavicon = null;

        session()->flash('notify', 'Branding updated successfully!');
        $this->dispatch('notify', 'Branding updated successfully!');
    }

    public function sendTestEmail()
    {
        $this->validate([
            'test_email' => 'required|email',
        ], [
            'test_email.required' => 'Email tujuan wajib diisi.',
        ]);

        try {
            // 1. Bersihkan instansi mailer
            \Illuminate\Support\Facades\Mail::purge('smtp');

            // 2. Set konfigurasi secara runtime
            $newConfig = [
                'transport' => 'smtp',
                'host' => $this->mail_host ?: config('mail.mailers.smtp.host'),
                'port' => $this->mail_port ?: config('mail.mailers.smtp.port'),
                'encryption' => $this->mail_encryption ?: config('mail.mailers.smtp.encryption'),
                'username' => $this->mail_username ?: config('mail.mailers.smtp.username'),
                'password' => $this->mail_password ?: config('mail.mailers.smtp.password'),
                'timeout' => 30,
                'stream' => [
                    'ssl' => [
                        'allow_self_signed' => true,
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ],
            ];

            config(['mail.mailers.smtp' => array_merge(config('mail.mailers.smtp'), $newConfig)]);
            config(['mail.from.address' => $this->mail_from_address ?: $this->mail_username]);
            config(['mail.from.name' => $this->mail_from_name ?: $this->organizer->name]);

            // 3. Paksa refresh mailer manager
            \Illuminate\Support\Facades\Mail::purge('smtp');

            // 4. Prepare Body with Diagnostic Info
            $body = "Hi! This is a test email for ORGANIZER Branding: " . $this->organizer->name . "\n\n" .
                    "--- SMTP DIAGNOSTIC INFO ---\n" .
                    "Host: " . ($this->mail_host ?: config('mail.mailers.smtp.host')) . "\n" .
                    "Port: " . ($this->mail_port ?: config('mail.mailers.smtp.port')) . "\n" .
                    "User: " . ($this->mail_username ?: config('mail.mailers.smtp.username')) . "\n" .
                    "Encryption: " . ($this->mail_encryption ?: config('mail.mailers.smtp.encryption') ?: 'none') . "\n" .
                    "From Address: " . ($this->mail_from_address ?: $this->mail_username ?: config('mail.from.address')) . "\n" .
                    "Timestamp: " . now()->toDateTimeString() . "\n" .
                    "----------------------------\n\n" .
                    "If you receive this, your SMTP configuration is correct. 🚀";

            // Kirim email pengetesan dengan Mail::raw
            \Illuminate\Support\Facades\Mail::raw($body, function ($message) {
                $message->to($this->test_email)->subject('Branding SMTP Verification');
            });

            $this->dispatch('notify', 'Test Email sent successfully! Check your inbox.');
        } catch (\Exception $e) {
            $this->dispatch('notify', 'SMTP Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.organizer.branding')->layout('layouts.app');
    }
}
