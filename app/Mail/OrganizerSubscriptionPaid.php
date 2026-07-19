<?php

namespace App\Mail;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class OrganizerSubscriptionPaid extends Mailable
{
    use Queueable, SerializesModels;

    public $transaction;
    public $organizer;
    public $plan;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
        $this->organizer = $transaction->payable;
        $this->plan = \App\Models\SubscriptionPlan::find($transaction->metadata['plan_id'] ?? null);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[' . config('settings.app_name') . '] Payment Successful - Invoice #' . $this->transaction->id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.organizer-subscription-paid',
        );
    }

    public function attachments(): array
    {
        // Generate PDF on the fly for attachment
        $data = [
            'transaction' => $this->transaction,
            'organizer' => $this->organizer,
            'plan' => $this->plan,
            'payment_method_detail' => $this->getPaymentMethodDetail(),
            'settings' => [
                'app_name' => config('settings.app_name', 'Registrasi.Events'),
                'address' => config('settings.footer_address', 'Address not set'),
                'email' => config('settings.footer_email', 'billing@registrasi.events'),
                'phone' => config('settings.footer_whatsapp', '+62 8xx xxxx xxxx'),
            ]
        ];

        $pdf = Pdf::loadView('admin.billing.invoice-pdf', $data);

        return [
            Attachment::fromData(fn () => $pdf->output(), 'INVOICE-' . $this->transaction->id . '.pdf')
                ->withMime('application/pdf'),
        ];
    }

    private function getPaymentMethodDetail()
    {
        $payload = json_decode($this->transaction->payload, true);
        $type = $this->transaction->payment_type;
        
        if (!$type) return 'Midtrans Gateway';

        switch ($type) {
            case 'bank_transfer':
                $bank = $payload['va_numbers'][0]['bank'] ?? ($payload['bank'] ?? 'Bank');
                return 'VA ' . strtoupper($bank);
            case 'qris':
                return 'QRIS (' . ($payload['acquirer'] ?? 'Universal') . ')';
            default:
                return str_replace('_', ' ', ucwords($type, '_'));
        }
    }
}
