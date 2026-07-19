<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class BillingInvoiceController extends Controller
{
    public function download($id)
    {
        $transaction = Transaction::where('id', $id)
            ->where('payable_type', \App\Models\Organizer::class)
            ->firstOrFail();

        // Security: Ensure the user belongs to the organizer of this transaction
        // unless they are super admin
        if (!auth()->user()->isSuperAdmin() && $transaction->organizer_id !== auth()->user()->organizer_id) {
            abort(403);
        }

        $organizer = $transaction->payable;
        $planId = $transaction->metadata['plan_id'] ?? null;
        $plan = \App\Models\SubscriptionPlan::find($planId);

        // Parsing Detailed Payment Method
        $payload = json_decode($transaction->payload, true);
        $paymentMethod = $this->formatPaymentMethod($transaction->payment_type, $payload);

        $data = [
            'transaction' => $transaction,
            'organizer' => $organizer,
            'plan' => $plan,
            'payment_method_detail' => $paymentMethod,
            'settings' => [
                'app_name' => config('settings.app_name', 'Registrasi.Events'),
                'address' => config('settings.footer_address', 'Address not set'),
                'email' => config('settings.footer_email', 'billing@registrasi.events'),
                'phone' => config('settings.footer_whatsapp', '+62 8xx xxxx xxxx'),
            ]
        ];

        $pdf = Pdf::loadView('admin.billing.invoice-pdf', $data);
        
        return $pdf->download('INVOICE-' . $transaction->id . '.pdf');
    }

    private function formatPaymentMethod($type, $payload)
    {
        if (!$type) return 'Midtrans Gateway';

        switch ($type) {
            case 'bank_transfer':
                $bank = $payload['va_numbers'][0]['bank'] ?? ($payload['bank'] ?? 'Bank');
                return 'VA ' . strtoupper($bank);
            case 'cstore':
                return strtoupper($payload['store'] ?? 'Retail Store');
            case 'credit_card':
                return 'Credit Card (' . ($payload['bank'] ?? 'Visa/Mastercard') . ')';
            case 'qris':
                return 'QRIS (' . ($payload['acquirer'] ?? 'Universal') . ')';
            case 'gopay':
                return 'GoPay';
            case 'shopeepay':
                return 'ShopeePay';
            case 'echannel':
                return 'Mandiri Bill Payment';
            default:
                return str_replace('_', ' ', ucwords($type, '_'));
        }
    }
}
