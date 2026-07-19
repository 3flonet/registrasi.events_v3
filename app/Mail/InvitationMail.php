<?php

namespace App\Mail;

use App\Traits\HasDynamicSmtp;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Invitation;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels, HasDynamicSmtp;

    public $invitation;
    public $event;
    public $confirmationLink;
    public $customSubject;
    public $customContent;

    public function __construct(Invitation $invitation, $content = null, $subject = null)
    {
        $this->invitation = $invitation;
        $this->event = $invitation->event;
        // Kita asumsikan nama route konfirmasi adalah 'invitation.confirm' (dibuat di langkah nanti)
        $this->confirmationLink = route('invitation.confirm', $invitation->uuid);
        $this->customContent = $content;
        $this->customSubject = $subject ?? 'Undangan Resmi: ' . $this->event->name;
    }

    public function build()
    {
        // 1. Apply SMTP settings
        $this->applyOrganizerSmtp($this->event->organizer);

        $organizer = $this->event->organizer;

        // 2. Branding Assets
        $logoPath = null;
        if ($organizer && $organizer->logo_path) {
            $logoPath = storage_path('app/public/' . $organizer->logo_path);
        } elseif (config('settings.app_logo')) {
            $logoPath = storage_path('app/public/' . config('settings.app_logo'));
        }

        // Use custom invitation banner if set, otherwise fallback to null (or event banner if you prefer)
        $bannerPath = $this->event->invitation_email_banner
            ? storage_path('app/public/' . $this->event->invitation_email_banner)
            : null;

        $appName = $organizer ? $organizer->name : config('app.name');

        return $this->subject($this->customSubject)
            ->view('emails.layouts.broadcast', [
                'subject'      => $this->customSubject,
                'content'      => $this->customContent,
                'appName'      => $appName,
                'logoPath'     => ($logoPath && file_exists($logoPath)) ? $logoPath : null,
                'bannerPath'   => ($bannerPath && file_exists($bannerPath)) ? $bannerPath : null,
                'primaryColor' => '#3b82f6',
                'secondaryColor' => '#1e293b',
            ]);
    }
}
