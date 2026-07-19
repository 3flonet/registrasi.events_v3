<?php

namespace App\Traits;

use App\Models\Organizer;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

trait HasDynamicSmtp
{
    /**
     * Flag to skip organizer SMTP and use default.
     */
    public bool $skipOrganizerSmtp = false;

    /**
     * Apply the dynamic SMTP configuration based on the organizer.
     * 
     * @param Organizer|null $organizer
     * @return $this
     */
    protected function applyOrganizerSmtp(?Organizer $organizer)
    {
        if (!$organizer || $this->skipOrganizerSmtp) {
            return $this;
        }

        $mailHost = $organizer->getSetting('mail_host');

        if ($mailHost) {
            $mailPort = $organizer->getSetting('mail_port', 587);
            $mailUsername = $organizer->getSetting('mail_username');
            $mailPassword = $organizer->getSetting('mail_password');
            $mailEncryption = $organizer->getSetting('mail_encryption');
            $mailFromAddr = $this->getDynamicFromAddress($organizer);
            $mailFromName = $this->getDynamicFromName($organizer);

            // Generate a unique mailer key
            $mailerKey = 'organizer_mailer_' . $organizer->id;

            // Purge existing instance of this specific mailer to ensure fresh config is used
            \Illuminate\Support\Facades\Mail::purge($mailerKey);

            // Inject configuration
            Config::set("mail.mailers.{$mailerKey}", [
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
                // CORRECT SSL CONFIGURATION
                'stream' => [
                    'ssl' => [
                        'allow_self_signed' => true,
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ],
            ]);

            // Set the mailer
            $this->mailer($mailerKey);
            $this->from($mailFromAddr, $mailFromName);
        }

        return $this;
    }

    /**
     * Get the resolved FROM address (Organizer vs System Default)
     */
    public function getDynamicFromAddress(?Organizer $organizer): string
    {
        if ($this->skipOrganizerSmtp || !$organizer) {
            return config('mail.from.address');
        }

        $organizerHost = $organizer->getSetting('mail_host');
        
        // If NO custom host, we MUST use the system default to avoid 553 errors
        if (empty($organizerHost)) {
            return config('mail.from.address');
        }

        // If HAS custom host, fallback to SMTP username if from_address is empty
        return $organizer->getSetting('mail_from_address') 
            ?: ($organizer->getSetting('mail_username') ?: config('mail.from.address'));
    }

    /**
     * Get the resolved FROM name (Organizer vs System Default)
     */
    public function getDynamicFromName(?Organizer $organizer): string
    {
        if ($this->skipOrganizerSmtp || !$organizer) {
            return config('mail.from.name');
        }

        $organizerHost = $organizer->getSetting('mail_host');
        if (empty($organizerHost)) {
            return config('mail.from.name');
        }

        return $organizer->getSetting('mail_from_name') ?: ($organizer->name ?? config('mail.from.name'));
    }
}
