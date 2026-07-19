<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Invitation;
use App\Mail\InvitationMail;
use Illuminate\Support\Facades\Mail;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

class SendInvitationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $invitation;
    protected $type;

    /**
     * Create a new job instance.
     */
    public function __construct(Invitation $invitation, $type = 'email')
    {
        $this->invitation = $invitation;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $event = $this->invitation->event;
        
        if (!$event) return;

        if ($this->type === 'email') {
            $this->sendEmail($event);
        } elseif ($this->type === 'whatsapp') {
            $this->sendWhatsApp($event);
        }
    }

    protected function sendEmail($event)
    {
        try {
            $parser = app(\App\Services\MessageParserService::class);
            
            // 1. Process Subject
            $subject = str_replace(
                ['{name}', '{event_name}', '{company}'],
                [$this->invitation->name, $event->name, $this->invitation->company ?? ''],
                $event->invitation_email_subject ?? "Undangan: " . $event->name
            );

            // 2. Process Content
            $processedBody = $parser->parseInvitation($event->invitation_email_body, $this->invitation);
            
            \App\Services\EmailService::send($this->invitation->email, new InvitationMail(
                $this->invitation, 
                $processedBody, 
                $subject
            ));

            $this->invitation->update([
                'is_sent_email' => true,
                'email_sent_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Email Broadcast Failed for ID ' . $this->invitation->id . ': ' . $e->getMessage());
        }
    }

    protected function sendWhatsApp($event)
    {
        $template = $event->invitation_wa_template;
        if (!$template) {
            $template = "Halo {name}, silakan konfirmasi kehadiran Anda di {link_konfirmasi}";
        }

        $parser = app(\App\Services\MessageParserService::class);
        $msg = $parser->parseInvitation($template, $this->invitation);

        try {
            $whatsapp = new WhatsAppService($event->organizer_id);
            $response = $whatsapp->sendMessage($this->invitation->phone_number, $msg);

            if (isset($response['status']) && $response['status'] == true) {
                $this->invitation->update([
                    'is_sent_whatsapp' => true,
                    'whatsapp_sent_at' => now(),
                ]);
            } else {
                Log::error('WhatsApp API Failure for ID ' . $this->invitation->id, ['response' => $response]);
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp Job Exception for ID ' . $this->invitation->id . ': ' . $e->getMessage());
        }
    }
}
