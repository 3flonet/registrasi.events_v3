<?php

namespace App\Mail;

use App\Models\Organizer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestBrandingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $organizer;
    public $appName;
    public $logoUrl;

    public function __construct(Organizer $organizer)
    {
        $this->organizer = $organizer;
        $this->appName = $organizer->name;
    }

    public function build()
    {
        $logoPath = null;
        if ($this->organizer->logo_path) {
            $logoPath = storage_path('app/public/' . $this->organizer->logo_path);
        }

        return $this->subject('🔔 Branding Test: ' . $this->appName)
                    ->view('emails.layouts.broadcast', [
                        'subject' => 'Branding Test: ' . $this->appName,
                        'appName' => $this->appName,
                        'logoPath' => ($logoPath && file_exists($logoPath)) ? $logoPath : null,
                        'bannerPath' => null,
                        'primaryColor' => '#3b82f6',
                        'secondaryColor' => '#1e293b',
                        'content' => '<p>Halo!</p><p>Ini adalah email percobaan untuk memverifikasi pengaturan branding Anda di <strong>' . $this->appName . '</strong>.</p><p>Jika Anda dapat melihat logo di atas dan nama organizer yang benar, berarti konfigurasi email dan branding Anda sudah <strong>AKTIF</strong>.</p><hr><p style="font-size: 12px; color: #777;">Email ini dikirim dari Dashboard Branding Organizer.</p>',
                    ]);
    }
}
