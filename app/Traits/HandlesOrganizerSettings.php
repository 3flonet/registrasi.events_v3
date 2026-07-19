<?php

namespace App\Traits;

use App\Models\Organizer;
use App\Models\Setting;
use Illuminate\Support\Facades\Config;

trait HandlesOrganizerSettings
{
    /**
     * Terapkan konfigurasi SMTP milik Organizer ke runtime Laravel.
     */
    public function applyOrganizerMailConfig($organizerId)
    {
        if (!$organizerId) return;

        $organizer = \App\Models\Organizer::find($organizerId);
        if (!$organizer) return;

        $mailHost = $organizer->getSetting('mail_host');

        if ($mailHost) {
            $mailPort = $organizer->getSetting('mail_port', 587);
            $mailUsername = $organizer->getSetting('mail_username');
            $mailPassword = $organizer->getSetting('mail_password');
            $mailEncryption = $organizer->getSetting('mail_encryption');
            $mailFromAddr = $organizer->getSetting('mail_from_address') ?: config('mail.from.address');
            $mailFromName = $organizer->getSetting('mail_from_name') ?: $organizer->name;

            $mailerKey = 'organizer_mailer_' . $organizer->id;

            // Purge existing instance to ensure fresh config is used
            \Illuminate\Support\Facades\Mail::purge($mailerKey);

            config(["mail.mailers.{$mailerKey}" => [
                'transport' => 'smtp',
                'host' => $mailHost,
                'port' => $mailPort,
                'encryption' => $mailEncryption ?: null,
                'username' => $mailUsername,
                'password' => $mailPassword,
                'timeout' => 30,
                'from' => [
                    'address' => $mailFromAddr,
                    'name' => $mailFromName,
                ],
                'stream' => [
                    'ssl' => [
                        'allow_self_signed' => true,
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ],
            ]]);

            // Terapkan ke instance Mailable saat ini (jika dipanggil dari class Mailable)
            if (method_exists($this, 'mailer')) {
                $this->mailer($mailerKey);
                $this->from($mailFromAddr, $mailFromName);
            }
        }
    }
}
