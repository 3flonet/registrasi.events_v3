<?php

namespace App\Livewire\Admin\Financial;

use App\Models\WithdrawalRequest;
use App\Models\OrganizerWallet;
use App\Models\WalletTransaction;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class WithdrawalManager extends Component
{
    use WithPagination;

    public $status = 'pending';
    public $search = '';
    public $rejectReason = '';
    public $selectedRequestId = null;

    protected $queryString = ['status', 'search'];

    public function completeRequest($id)
    {
        $request = WithdrawalRequest::findOrFail($id);
        
        if ($request->status !== 'pending') {
            session()->flash('error', 'Request has already been processed.');
            return;
        }

        DB::transaction(function () use ($request) {
            // 1. Mark as completed
            $request->update([
                'status' => 'completed',
                'processed_at' => now(),
            ]);

            // 2. Move funds from pending to total withdrawn
            $wallet = OrganizerWallet::where('organizer_id', $request->organizer_id)->first();
            $wallet->decrement('pending_withdrawal', $request->amount_requested);
            $wallet->increment('total_withdrawn', $request->final_amount);

            // 3. Notify Organizer Users
            $organizerUsers = $request->organizer->users;
            \Illuminate\Support\Facades\Notification::send($organizerUsers, new \App\Notifications\WithdrawalProcessedNotification($request));
        });

        session()->flash('success', 'Withdrawal marked as completed and processed.');
    }

    public function confirmReject($id)
    {
        $this->selectedRequestId = $id;
        $this->dispatch('show-reject-modal');
    }

    public function rejectRequest()
    {
        $this->validate([
            'rejectReason' => 'required|string|min:5'
        ]);

        $request = WithdrawalRequest::findOrFail($this->selectedRequestId);
        
        if ($request->status !== 'pending') return;

        DB::transaction(function () use ($request) {
            // 1. Mark as rejected
            $request->update([
                'status' => 'rejected',
                'processed_at' => now(),
                'admin_note' => $this->rejectReason
            ]);

            // 2. Refund balance back to active balance
            $wallet = OrganizerWallet::where('organizer_id', $request->organizer_id)->first();
            $wallet->decrement('pending_withdrawal', $request->amount_requested);
            $wallet->increment('balance', $request->amount_requested);

            // 3. Record Refund Transaction
            WalletTransaction::create([
                'organizer_id' => $request->organizer_id,
                'type' => 'credit',
                'amount' => $request->amount_requested,
                'fee_amount' => 0,
                'net_amount' => $request->amount_requested,
                'description' => 'REFUND: Withdrawal Rejected - ' . $this->rejectReason,
            ]);

            // 4. Notify Organizer Users
            $organizerUsers = $request->organizer->users;
            \Illuminate\Support\Facades\Notification::send($organizerUsers, new \App\Notifications\WithdrawalProcessedNotification($request));
        });

        $this->reset(['rejectReason', 'selectedRequestId']);
        $this->dispatch('hide-reject-modal');
        session()->flash('success', 'Withdrawal request rejected and funds refunded to organizer.');
    }

    public function render()
    {
        $requests = WithdrawalRequest::query()
            ->with('organizer')
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->search, function($q) {
                $q->whereHas('organizer', fn($sq) => $sq->where('name', 'like', "%{$this->search}%"))
                  ->orWhere('bank_account_number', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(15);

        // Financial Summary for Header Widgets
        $saasRegistrationRevenue = \App\Models\Transaction::where('status', 'paid')
            ->where('gateway_type', 'system')
            ->where('payable_type', 'App\Models\Registration')
            ->sum('amount');
            
        $totalOrganizerBalance = OrganizerWallet::sum('balance') + OrganizerWallet::sum('pending_withdrawal');
        $totalPendingAmount = WithdrawalRequest::where('status', 'pending')->sum('final_amount');

        return view('livewire.admin.financial.withdrawal-manager', [
            'requests' => $requests,
            'saasRegistrationRevenue' => $saasRegistrationRevenue,
            'totalOrganizerBalance' => $totalOrganizerBalance,
            'totalPendingAmount' => $totalPendingAmount
        ])->layout('layouts.app');
    }
}
