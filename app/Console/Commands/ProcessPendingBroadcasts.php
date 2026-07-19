<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PendingBroadcast;
use App\Models\PendingEventBroadcast;
use App\Models\EventEmailTemplate;
use App\Models\Registration;
use App\Mail\GlobalBroadcastMail;
use App\Mail\DynamicBroadcastMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProcessPendingBroadcasts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'broadcasts:process-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending global and event-specific broadcasts.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Mulai memproses broadcast yang tertunda...');

        // 1. Proses Global Broadcasts
        $this->processGlobalBroadcasts();

        // 2. Proses Event Broadcasts
        $this->processEventBroadcasts();

        $this->info('Semua broadcast yang tertunda telah selesai diproses.');
        return 0;
    }

    /**
     * Process pending global broadcasts.
     */
    private function processGlobalBroadcasts()
    {
        // Ambil satu tugas global yang sedang 'pending' atau 'processing' untuk dilanjutkan
        $broadcast = PendingBroadcast::whereIn('status', ['pending', 'processing'])->first();

        if (!$broadcast) {
            $this->line('Tidak ada Global Broadcast yang perlu diproses.');
            return;
        }

        $this->line("Memproses Global Broadcast ID: {$broadcast->id}");
        
        if ($broadcast->status === 'pending') {
            $broadcast->update(['status' => 'processing']);
        }

        try {
            $template = $broadcast->template;
            if (!$template) {
                throw new \Exception("Template dengan ID {$broadcast->template_id} tidak ditemukan.");
            }

            // Ambil penerima secara bertahap (batch) 
            $batchSize = 50;
            
            if ($broadcast->target === 'organizers') {
                if ($broadcast->type === 'whatsapp') {
                    $recipients = \App\Models\Organizer::query()
                        ->whereNotNull('phone')
                        ->where('phone', '!=', '')
                        ->whereIn('id', function($query) {
                            $query->select(DB::raw('MAX(id)'))
                                ->from('organizers')
                                ->groupBy('phone');
                        })
                        ->skip($broadcast->processed_count)
                        ->take($batchSize)
                        ->get()
                        ->map(function($org) {
                            return (object)[
                                'id' => $org->id,
                                'name' => $org->name,
                                'email' => $org->email,
                                'phone_number' => $org->phone,
                                'event' => null,
                                'ticketTier' => null,
                                'uuid' => null,
                                'total_price' => 0,
                                'attendance_type' => 'offline',
                                'checked_in_at' => null,
                                'submission' => null,
                            ];
                        });
                } else {
                    $recipients = \App\Models\Organizer::query()
                        ->whereNotNull('email')
                        ->where('email', '!=', '')
                        ->whereIn('id', function($query) {
                            $query->select(DB::raw('MAX(id)'))
                                ->from('organizers')
                                ->groupBy('email');
                        })
                        ->skip($broadcast->processed_count)
                        ->take($batchSize)
                        ->get()
                        ->map(function($org) {
                            return (object)[
                                'id' => $org->id,
                                'name' => $org->name,
                                'email' => $org->email,
                                'phone_number' => $org->phone,
                                'event' => null,
                                'ticketTier' => null,
                                'uuid' => null,
                                'total_price' => 0,
                                'attendance_type' => 'offline',
                                'checked_in_at' => null,
                                'submission' => null,
                            ];
                        });
                }
            } else {
                // Attendees (default)
                if ($broadcast->type === 'whatsapp') {
                    $recipients = Registration::query()
                        ->with('event')
                        ->whereNotNull('phone_number')
                        ->where('phone_number', '!=', '')
                        ->whereIn('id', function($query) {
                            $query->select(DB::raw('MAX(id)'))
                                ->from('registrations')
                                ->groupBy('phone_number');
                        })
                        ->skip($broadcast->processed_count)
                        ->take($batchSize)
                        ->get();
                } else {
                    $recipients = Registration::query()
                        ->with('event')
                        ->whereNotNull('email')
                        ->where('email', '!=', '')
                        ->whereIn('id', function($query) {
                            $query->select(DB::raw('MAX(id)'))
                                ->from('registrations')
                                ->groupBy('email');
                        })
                        ->skip($broadcast->processed_count)
                        ->take($batchSize)
                        ->get();
                }
            }

            if ($recipients->isEmpty()) {
                $broadcast->update(['status' => 'completed']);
                $this->info("Global Broadcast ID: {$broadcast->id} selesai.");
                return;
            }

            foreach ($recipients as $recipient) {
                if ($broadcast->type === 'whatsapp') {
                    // --- WHATSAPP BROADCAST ---
                    if ($recipient->phone_number) {
                        try {
                            $whatsapp = app(\App\Services\WhatsAppService::class);
                            $parser = app(\App\Services\MessageParserService::class);

                            if ($template->whatsapp_template_id && $template->whatsappTemplate) {
                                $whatsappTemplate = $template->whatsappTemplate;
                                
                                $bodyParams = [];
                                if (isset($whatsappTemplate->parameters['body'])) {
                                    $bodyParams = $parser->parseParameters($whatsappTemplate->parameters['body'], $recipient);
                                }

                                $headerParam = null;
                                if (isset($whatsappTemplate->parameters['header']) && $whatsappTemplate->parameters['header']) {
                                    $headerType = $whatsappTemplate->parameters['header']['type'];
                                    $headerKey = $whatsappTemplate->parameters['header']['value'];
                                    $resolvedVal = $parser->parseParameters([$headerKey], $recipient)[0] ?? null;
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

                                $buttonParams = [];
                                if (isset($whatsappTemplate->parameters['buttons'])) {
                                    $customMapping = $template->whatsapp_buttons_mapping ?? [];
                                    foreach ($whatsappTemplate->parameters['buttons'] as $btn) {
                                        $idx = $btn['index'];
                                        $mapVal = $customMapping[$idx] ?? ($btn['value'] ?? null);
                                        if ($mapVal) {
                                            $resolvedBtnVal = $parser->parseParameters([$mapVal], $recipient)[0] ?? null;
                                            if ($resolvedBtnVal) {
                                                if (($mapVal === 'ticket_url' || $mapVal === 'payment_link') && !empty($recipient->uuid)) {
                                                    $resolvedBtnVal = $recipient->uuid;
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
                                    $recipient->phone_number,
                                    $whatsappTemplate->name,
                                    $whatsappTemplate->language_code,
                                    $payloadParams
                                );
                            } else {
                                $msgSource = !empty($template->whatsapp_content) 
                                    ? $template->whatsapp_content 
                                    : strip_tags(str_replace(['<br>', '<br/>', '</p>'], ["\n", "\n", "\n\n"], $template->content));
                                $finalMsg = $parser->parse($msgSource, $recipient, $template);
                                $whatsapp->sendMessage($recipient->phone_number, $finalMsg);
                            }
                        } catch (Throwable $e) {
                            Log::error("WA Broadcast Item Error: " . $e->getMessage());
                        }
                    }
                } else {
                    // --- EMAIL BROADCAST (Default) ---
                    Mail::to($recipient->email)->queue(new GlobalBroadcastMail($template, $recipient));
                }
            }

            // Update progress
            $broadcast->increment('processed_count', $recipients->count());
            $this->info("{$recipients->count()} penerima untuk Global Broadcast ID: {$broadcast->id} telah dimasukkan ke antrean.");

        } catch (Throwable $e) {
            $broadcast->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            $this->error("Gagal memproses Global Broadcast ID: {$broadcast->id}. Error: " . $e->getMessage());
            Log::error("Global Broadcast Error (ID: {$broadcast->id}): " . $e->getMessage());
        }
    }

    /**
     * Process pending event-specific broadcasts.
     */
    private function processEventBroadcasts()
    {
        // Ambil satu tugas event broadcast yang sedang 'pending' atau 'processing'
        $broadcast = PendingEventBroadcast::whereIn('status', ['pending', 'processing'])->first();

        if (!$broadcast) {
            $this->line('Tidak ada Broadcast Acara yang perlu diproses.');
            return;
        }

        $this->line("Memproses Broadcast Acara ID: {$broadcast->id} untuk Acara ID: {$broadcast->event_id}");

        if ($broadcast->status === 'pending') {
            $broadcast->update(['status' => 'processing']);
        }
        
        try {
            $template = $broadcast->template;
            if (!$template) {
                throw new \Exception("Template dengan ID {$broadcast->template_id} tidak ditemukan.");
            }

            // Ambil pendaftar acara ini secara bertahap (batch)
            $batchSize = 50; // Kirim 50 email per eksekusi
            $query = Registration::where('event_id', $broadcast->event_id);

            // Filtering: Jika kategori adalah feedback atau certificate, hanya kirim ke yang sudah hadir
            if (in_array($template->category, ['feedback', 'certificate'])) {
                $query->whereNotNull('checked_in_at');
            }

            $registrations = $query->skip($broadcast->progress)
                ->take($batchSize)
                ->get();

            if ($registrations->isEmpty()) {
                $broadcast->update(['status' => 'completed']);
                $this->info("Broadcast Acara ID: {$broadcast->id} selesai.");
                return;
            }
            
            foreach ($registrations as $registration) {
                if ($broadcast->type === 'whatsapp') {
                    // --- WHATSAPP BROADCAST ---
                    if ($registration->phone_number) {
                        try {
                            $whatsapp = app(\App\Services\WhatsAppService::class);
                            $parser = app(\App\Services\MessageParserService::class);

                            if ($template->whatsapp_template_id && $template->whatsappTemplate) {
                                $whatsappTemplate = $template->whatsappTemplate;
                                
                                $bodyParams = [];
                                if (isset($whatsappTemplate->parameters['body'])) {
                                    $bodyParams = $parser->parseParameters($whatsappTemplate->parameters['body'], $registration);
                                }

                                $headerParam = null;
                                if (isset($whatsappTemplate->parameters['header']) && $whatsappTemplate->parameters['header']) {
                                    $headerType = $whatsappTemplate->parameters['header']['type'];
                                    $headerKey = $whatsappTemplate->parameters['header']['value'];
                                    $resolvedVal = $parser->parseParameters([$headerKey], $registration)[0] ?? null;
                                    if ($resolvedVal) {
                                        $headerParam = [
                                            'type' => $headerType,
                                            'value' => $resolvedVal,
                                            'filename' => 'Tiket.pdf'
                                        ];
                                    }
                                }

                                $buttonParams = [];
                                if (isset($whatsappTemplate->parameters['buttons'])) {
                                    $customMapping = $template->whatsapp_buttons_mapping ?? [];
                                    foreach ($whatsappTemplate->parameters['buttons'] as $btn) {
                                        $idx = $btn['index'];
                                        $mapVal = $customMapping[$idx] ?? ($btn['value'] ?? null);
                                        if ($mapVal) {
                                            $resolvedBtnVal = $parser->parseParameters([$mapVal], $registration)[0] ?? null;
                                            if ($resolvedBtnVal) {
                                                if ($mapVal === 'ticket_url' || $mapVal === 'payment_link') {
                                                    $resolvedBtnVal = $registration->uuid;
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
                                    $registration->phone_number,
                                    $whatsappTemplate->name,
                                    $whatsappTemplate->language_code,
                                    $payloadParams
                                );
                            } else {
                                $msgSource = !empty($template->whatsapp_content) 
                                    ? $template->whatsapp_content 
                                    : strip_tags(str_replace(['<br>', '<br/>', '</p>'], ["\n", "\n", "\n\n"], $template->content));
                                $finalMsg = $parser->parse($msgSource, $registration, $template);
                                $whatsapp->sendMessage($registration->phone_number, $finalMsg);
                            }
                        } catch (Throwable $e) {
                            Log::error("WA Event Broadcast Item Error: " . $e->getMessage());
                        }
                    }
                } else {
                    // --- EMAIL BROADCAST (Default) ---
                    Mail::to($registration->email)->queue(new DynamicBroadcastMail($template, $registration));
                }
            }

            // Update progress
            $broadcast->increment('progress', $registrations->count());
            $this->info("{$registrations->count()} email untuk Broadcast Acara ID: {$broadcast->id} telah dimasukkan ke antrean.");

        } catch (Throwable $e) {
            $broadcast->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            $this->error("Gagal memproses Broadcast Acara ID: {$broadcast->id}. Error: " . $e->getMessage());
            Log::error("Event Broadcast Error (ID: {$broadcast->id}): " . $e->getMessage());
        }
    }
}