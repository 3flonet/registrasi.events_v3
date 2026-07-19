<?php

namespace App\Livewire\Admin\Organizer;

use App\Models\OrganizerWallet;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use App\Models\Setting;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class Wallet extends Component
{
    use WithPagination;

    public $showWithdrawModal = false;
    
    // Withdraw Form Fields
    public $amount;
    public $bankName, $bankAccountNumber, $bankAccountName;
    
    // Summary data
    public $balance = 0;
    public $totalWithdrawn = 0;
    public $pendingWithdrawal = 0;
    public $typeFilter = '';

    protected $queryString = [
        'typeFilter' => ['except' => '']
    ];

    protected $listeners = ['refreshWallet' => '$refresh'];

    public function mount()
    {
        $this->loadWalletData();
    }

    public function loadWalletData()
    {
        $wallet = OrganizerWallet::firstOrCreate(
            ['organizer_id' => auth()->user()->organizer_id],
            ['balance' => 0]
        );
        $this->balance = $wallet->balance;
        $this->totalWithdrawn = $wallet->total_withdrawn;
        $this->pendingWithdrawal = $wallet->pending_withdrawal;
    }

    /**
     * Menghitung estimasi yang akan diterima (Transparansi)
     */
    public function getWithdrawalPreviewProperty()
    {
        if (!$this->amount || !is_numeric($this->amount)) return null;

        $fee = Setting::withoutGlobalScopes()->where('key', 'withdrawal_fee')->whereNull('organizer_id')->first()?->value ?? 0;
        $final = max(0, $this->amount - $fee);

        return [
            'requested' => (float)$this->amount,
            'fee' => (float)$fee,
            'final' => (float)$final
        ];
    }

    public function submitWithdrawal()
    {
        $this->validate([
            'amount' => 'required|numeric|min:50000|max:' . $this->balance,
            'bankName' => 'required|string|max:100',
            'bankAccountNumber' => 'required|string|max:50',
            'bankAccountName' => 'required|string|max:100',
        ]);

        $preview = $this->withdrawal_preview;

        DB::transaction(function () use ($preview) {
            $wallet = OrganizerWallet::where('organizer_id', auth()->user()->organizer_id)->first();
            
            // 1. Catat Pengajuan
            $withdrawalRequest = WithdrawalRequest::create([
                'organizer_id' => auth()->user()->organizer_id,
                'amount_requested' => $preview['requested'],
                'withdrawal_fee' => $preview['fee'],
                'final_amount' => $preview['final'],
                'bank_name' => $this->bankName,
                'bank_account_number' => $this->bankAccountNumber,
                'bank_account_name' => $this->bankAccountName,
                'status' => 'pending'
            ]);

            // 2. Pindahkan Saldo ke Pending
            $wallet->decrement('balance', $preview['requested']);
            $wallet->increment('pending_withdrawal', $preview['requested']);

            // 3. Catat ke Riwayat Transaksi (Status: Pending)
            WalletTransaction::create([
                'organizer_id' => auth()->user()->organizer_id,
                'type' => 'debit',
                'amount' => $preview['requested'],
                'fee_amount' => $preview['fee'],
                'net_amount' => $preview['final'],
                'description' => 'Withdrawal Request to ' . $this->bankName . ' (' . $this->bankAccountNumber . ')',
                'metadata' => [
                    'withdrawal_request_id' => $withdrawalRequest->id,
                    'bank_details' => [
                        'bank' => $this->bankName,
                        'number' => $this->bankAccountNumber,
                        'name' => $this->bankAccountName
                    ]
                ]
            ]);

            // 4. Notify Super Admins
            $superAdmins = \App\Models\User::role('Super Admin')->get();
            \Illuminate\Support\Facades\Notification::send($superAdmins, new \App\Notifications\WithdrawalRequestedNotification($withdrawalRequest));
        });

        $this->reset(['amount', 'showWithdrawModal']);
        $this->loadWalletData();
        
        session()->flash('success', 'Withdrawal request submitted! Super Admin will process your transfer soon.');
    }

    public function render()
    {
        $transactions = WalletTransaction::where('organizer_id', auth()->user()->organizer_id)
            ->when($this->typeFilter, fn($q) => $q->where('type', $this->typeFilter))
            ->latest()
            ->paginate(10);

        // Fetch statuses for withdrawals in this page for efficiency
        $withdrawalIds = collect($transactions->items())
            ->map(fn($t) => $t->metadata['withdrawal_request_id'] ?? null)
            ->filter()
            ->values();
            
        $withdrawalStatuses = WithdrawalRequest::whereIn('id', $withdrawalIds)->pluck('status', 'id')->toArray();

        // Fallback logic for existing transactions without withdrawal_request_id in metadata
        foreach ($transactions->items() as $t) {
            if ($t->type === 'debit' && !isset($t->metadata['withdrawal_request_id'])) {
                $match = WithdrawalRequest::where('organizer_id', $t->organizer_id)
                    ->where('final_amount', $t->net_amount)
                    // Cari yang dibuat dalam rentang waktu 5 menit dari transaksi
                    ->whereBetween('created_at', [$t->created_at->subMinutes(5), $t->created_at->addMinutes(5)])
                    ->first();
                
                if ($match) {
                    // Inject ID secara temporary untuk ditampilkan di view
                    $metadata = $t->metadata;
                    $metadata['withdrawal_request_id'] = $match->id;
                    $t->metadata = $metadata;
                    
                    // Tambahkan status ke map jika belum ada
                    if (!isset($withdrawalStatuses[$match->id])) {
                        $withdrawalStatuses[$match->id] = $match->status;
                    }
                }
            }
        }

        return view('livewire.admin.organizer.wallet', [
            'transactions' => $transactions,
            'withdrawalStatuses' => $withdrawalStatuses
        ])->layout('layouts.app');
    }
}
