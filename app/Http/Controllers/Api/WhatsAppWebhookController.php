<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    protected $whatsapp;

    public function __construct(WhatsAppService $whatsapp)
    {
        $this->whatsapp = $whatsapp;
    }

    public function handle(Request $request)
    {
        // 1. Tangani verifikasi dari Meta (GET Request)
        if ($request->isMethod('GET')) {
            $verifyToken = 'registrasi_events_token';
            
            $mode = $request->query('hub_mode');
            $token = $request->query('hub_verify_token');
            $challenge = $request->query('hub_challenge');
            
            if ($mode && $token) {
                if ($mode === 'subscribe' && $token === $verifyToken) {
                    Log::info('WhatsApp Webhook Verified Successfully');
                    return response($challenge, 200)->header('Content-Type', 'text/plain');
                }
            }
            
            Log::warning('WhatsApp Webhook Verification Failed', [
                'mode' => $mode,
                'token' => $token
            ]);
            return response('Forbidden', 403);
        }

        // 2. Tangani pesan masuk dari Meta (POST Request)
        Log::info('WhatsApp Webhook Received Payload', $request->all());

        $entry = $request->input('entry', []);
        if (!empty($entry)) {
            $changes = $entry[0]['changes'] ?? [];
            if (!empty($changes)) {
                $value = $changes[0]['value'] ?? [];
                $messages = $value['messages'] ?? [];
                if (!empty($messages)) {
                    $message = $messages[0];
                    $sender = $message['from'] ?? null;
                    
                    if ($sender && isset($message['text']['body'])) {
                        $rawMessage = trim($message['text']['body']);
                        $messageUpper = strtoupper($rawMessage);
                        
                        if (str_starts_with($messageUpper, 'TICKET_')) {
                            $uuid = substr($rawMessage, 7);
                            $this->replyWithTicket($sender, $uuid);
                        }
                    }
                }
            }
        }

        return response()->json(['status' => 'success']);
    }

    protected function replyWithTicket($sender, $uuid)
    {
        $registration = Registration::where('uuid', $uuid)->first();

        if ($registration) {
            $name = $registration->name;
            $event = $registration->event->name;
            $ticketUrl = route('invoice.show', $registration->uuid); // Or a specific ticket route

            $reply = "Halo *{$name}*!\n\n";
            $reply .= "Berikut adalah link tiket Anda untuk event *{$event}*:\n";
            $reply .= "{$ticketUrl}\n\n";
            $reply .= "Silakan klik link di atas untuk melihat detail pendaftaran dan QR Code Anda.";

            $this->whatsapp->sendMessage($sender, $reply);
        } else {
            $this->whatsapp->sendMessage($sender, "Maaf, pendaftaran dengan ID tersebut tidak ditemukan. Silakan cek kembali link Anda.");
        }
    }
}
