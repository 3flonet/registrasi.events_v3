<?php

namespace App\Notifications;

use App\Models\WithdrawalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;

class WithdrawalProcessedNotification extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;

    public WithdrawalRequest $withdrawalRequest;

    public function __construct(WithdrawalRequest $withdrawalRequest)
    {
        $this->withdrawalRequest = $withdrawalRequest;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $status = strtoupper($this->withdrawalRequest->status);
        $amount = number_format($this->withdrawalRequest->final_amount, 0);
        $url = route('admin.wallet.index');

        $message = (new MailMessage)
            ->subject("Withdrawal Request {$status}")
            ->greeting("Hello, {$notifiable->name}!");

        if ($this->withdrawalRequest->status === 'completed') {
            $message->line("Good news! Your withdrawal request of **IDR {$amount}** has been **PROCESSED**.")
                    ->line("Funds have been transferred to your **{$this->withdrawalRequest->bank_name}** account.")
                    ->line("Please check your bank statement.");
        } else {
            $message->line("Your withdrawal request of **IDR " . number_format($this->withdrawalRequest->amount_requested, 0) . "** has been **REJECTED**.")
                    ->line("Reason: **" . ($this->withdrawalRequest->admin_note ?? 'No reason provided') . "**")
                    ->line("The funds have been refunded to your active balance.");
        }

        return $message->action('View My Wallet', $url)
                       ->line('Thank you for using our platform!');
    }

    public function toArray(object $notifiable): array
    {
        $status = $this->withdrawalRequest->status;
        $isCompleted = $status === 'completed';

        return [
            'title' => $isCompleted ? 'Withdrawal Completed' : 'Withdrawal Rejected',
            'message' => $isCompleted 
                ? 'Your payout of IDR ' . number_format($this->withdrawalRequest->final_amount, 0) . ' has been processed!'
                : 'Your withdrawal request was rejected. Reason: ' . ($this->withdrawalRequest->admin_note ?? '-'),
            'url' => route('admin.wallet.index'),
            'type' => 'withdrawal_processed',
            'status' => $status,
            'amount' => $isCompleted ? $this->withdrawalRequest->final_amount : $this->withdrawalRequest->amount_requested,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
