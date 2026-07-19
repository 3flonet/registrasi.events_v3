<?php

namespace App\Jobs;

use App\Models\Registration;
use App\Models\Event;
use App\Models\EventEmailTemplate;
use App\Services\WhatsAppService;
use App\Services\MessageParserService;
use App\Mail\DynamicBroadcastMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendCheckinNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $registration;
    protected $event;
    protected $templateId;

    /**
     * Create a new job instance.
     */
    public function __construct(Registration $registration, Event $event, $templateId)
    {
        $this->registration = $registration;
        $this->event = $event;
        $this->templateId = $templateId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $template = EventEmailTemplate::find($this->templateId);
        if (!$template) {
            return;
        }

        // --- KIRIM WHATSAPP ---
        if ($this->registration->phone_number && ($template->whatsapp_template_id || !empty($template->whatsapp_content))) {
            try {
                $whatsapp = app(WhatsAppService::class);
                $parser = app(MessageParserService::class);
                
                if ($template->whatsapp_template_id && $template->whatsappTemplate) {
                    $whatsappTemplate = $template->whatsappTemplate;
                    
                    // Resolve body parameters
                    $bodyParams = [];
                    if (isset($whatsappTemplate->parameters['body'])) {
                        $bodyParams = $parser->parseParameters($whatsappTemplate->parameters['body'], $this->registration);
                    }

                    // Resolve header parameters
                    $headerParam = null;
                    if (isset($whatsappTemplate->parameters['header']) && $whatsappTemplate->parameters['header']) {
                        $headerType = $whatsappTemplate->parameters['header']['type'];
                        $headerKey = $whatsappTemplate->parameters['header']['value'];
                        $resolvedVal = $parser->parseParameters([$headerKey], $this->registration)[0] ?? null;
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

                    // Resolve button parameters
                    $buttonParams = [];
                    if (isset($whatsappTemplate->parameters['buttons'])) {
                        $customMapping = $template->whatsapp_buttons_mapping ?? [];
                        foreach ($whatsappTemplate->parameters['buttons'] as $btn) {
                            $idx = $btn['index'];
                            $mapVal = $customMapping[$idx] ?? ($btn['value'] ?? null);
                            if ($mapVal) {
                                $resolvedBtnVal = $parser->parseParameters([$mapVal], $this->registration)[0] ?? null;
                                if ($resolvedBtnVal) {
                                    if ($mapVal === 'ticket_url' || $mapVal === 'payment_link') {
                                        $resolvedBtnVal = $this->registration->uuid;
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
                        $this->registration->phone_number,
                        $whatsappTemplate->name,
                        $whatsappTemplate->language_code,
                        $payloadParams
                    );
                } else {
                    $msg = $parser->parse($template->whatsapp_content, $this->registration, $template);
                    $whatsapp->sendMessage($this->registration->phone_number, $msg);
                }
            } catch (\Exception $e) {
                Log::error('Auto Checkin WA Queue Error: ' . $e->getMessage());
            }
        }

        // --- KIRIM EMAIL ---
        if ($this->registration->email) {
            try {
                Mail::to($this->registration->email)->send(new DynamicBroadcastMail($template, $this->registration));
            } catch (\Exception $e) {
                Log::error('Auto Checkin Email Queue Error: ' . $e->getMessage());
            }
        }
    }
}
