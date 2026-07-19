<?php

namespace App\Services;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailService
{
    /**
     * Send email with Smart Fallback (Retry using default SMTP if organizer SMTP fails)
     */
    public static function send($to, Mailable $mailable)
    {
        try {
            // Attempt 1: Using Organizer SMTP (default behavior in our Mailable)
            return Mail::to($to)->send($mailable);
        } catch (\Exception $e) {
            Log::warning("Email Smart Fallback Triggered for {$to}. Error: " . $e->getMessage());

            // 1. Force the default mailer
            $mailable->mailer(config('mail.default'));
            
            // 2. Set the flag to skip re-applying organizer SMTP if build() or envelope() is called again
            if (property_exists($mailable, 'skipOrganizerSmtp')) {
                $mailable->skipOrganizerSmtp = true;
            }

            // 3. Attempt 2: Using System Default SMTP
            try {
                return Mail::to($to)->send($mailable);
            } catch (\Exception $fallbackException) {
                Log::error("CRITICAL: Email Fallback failed for {$to}. Error: " . $fallbackException->getMessage());
                throw $fallbackException; // Rethrow if even the default fails
            }
        }
    }
}
