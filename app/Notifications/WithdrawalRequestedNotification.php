<?php

namespace App\Notifications;

use App\Models\WithdrawalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;

class WithdrawalRequestedNotification extends Notification implements ShouldBroadcast, ShouldQueue
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
        $organizerName = $this->withdrawalRequest->organizer->name;
        $amount = number_format($this->withdrawalRequest->amount_requested, 0);
        $url = route('admin.withdrawals.index');

        return (new MailMessage)
            ->subject("New Withdrawal Request: {$organizerName}")
            ->greeting("Hello, Admin!")
            ->line("Organizer **{$organizerName}** has requested a withdrawal of **IDR {$amount}**.")
            ->line("Bank: **{$this->withdrawalRequest->bank_name}**")
            ->line("Account: **{$this->withdrawalRequest->bank_account_number}** ({$this->withdrawalRequest->bank_account_name})")
            ->action('Review Withdrawal Request', $url)
            ->line('Please process this transfer soon.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Withdrawal Request',
            'message' => 'Organizer ' . $this->withdrawalRequest->organizer->name . ' requested IDR ' . number_format($this->withdrawalRequest->amount_requested, 0),
            'url' => route('admin.withdrawals.index'),
            'type' => 'withdrawal_request',
            'amount' => $this->withdrawalRequest->amount_requested,
            'organizer_name' => $this->withdrawalRequest->organizer->name,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
