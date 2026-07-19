<?php

namespace App\Services;

use App\Models\Registration;
use App\Models\EventEmailTemplate;
use App\Mail\DynamicBroadcastMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class RegistrationService
{
    /**
     * Konfirmasi pembayaran pendaftaran dan kirim notifikasi.
     * Digunakan oleh PaymentCallbackController dan manual check di Invoice page.
     */
    public function confirmPayment(Registration $registration)
    {
        // Hanya proses jika belum dikonfirmasi
        if ($registration->payment_status === 'paid') {
            return;
        }

        return \DB::transaction(function () use ($registration) {
            // 1. Update Status
            $registration->update([
                'payment_status' => 'paid',
                'status' => 'confirmed'
            ]);

            // 2. Update Transaksi Terkait jika ada
            if ($registration->transaction) {
                $registration->transaction->update(['status' => 'paid']);
            }

            // 3. Kirim Email Tiket
            try {
                $event = $registration->event;
                if ($event && $event->confirmation_template_id) {
                    $template = EventEmailTemplate::find($event->confirmation_template_id);
                    if ($template) {
                        Mail::to($registration->email)->send(new DynamicBroadcastMail($template, $registration));
                        Log::info('Ticket email sent for registration ' . $registration->uuid);
                    }
                }
            } catch (\Exception $e) {
                Log::error('Failed sending ticket email: ' . $e->getMessage());
            }

            // 4. Kirim WhatsApp Tiket
            try {
                if ($registration->phone_number) {
                    $whatsapp = new \App\Services\WhatsAppService($registration->event->organizer_id);
                    $event = $registration->event;
                    $msg = null;

                    // Cek jika ada template konfirmasi (Email & WA biasanya jadi satu di EventEmailTemplate)
                    if ($event && $event->confirmation_template_id) {
                        $template = EventEmailTemplate::find($event->confirmation_template_id);
                        if ($template && $template->whatsapp_content) {
                            $parser = new \App\Services\MessageParserService();
                            $msg = $parser->parse($template->whatsapp_content, $registration, $template);
                        }
                    }

                    // Fallback jika tidak ada template
                    if (!$msg) {
                        $ticketUrl = route('invoice.show', $registration->uuid);
                        $msg = "📢 *Pembayaran Berhasil!*\n\n";
                        $msg .= "Terima kasih, pembayaran Anda untuk event *{$registration->event->name}* telah kami terima.\n\n";
                        $msg .= "Link Tiket/QR Code Anda:\n{$ticketUrl}\n\n";
                        $msg .= "_Note: Jika link tidak dapat diklik, pastikan nomor ini sudah disimpan di kontak Anda atau silakan balas pesan ini._";
                    }

                    $whatsapp->sendMessage($registration->phone_number, $msg);
                    Log::info('Ticket WhatsApp sent for registration ' . $registration->uuid);
                }
            } catch (\Exception $e) {
                Log::error('Failed sending ticket WhatsApp: ' . $e->getMessage());
            }

            // 5. Credit Wallet Organizer
            try {
                if ($registration->transaction) {
                    $walletService = new WalletService();
                    $walletService->creditRegistration($registration->transaction->id);
                }
            } catch (\Exception $e) {
                Log::error('Failed crediting wallet: ' . $e->getMessage());
            }

            return $registration;
        });
    }
}
