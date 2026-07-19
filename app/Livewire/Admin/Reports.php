<?php

namespace App\Livewire\Admin;

use App\Models\Transaction;
use App\Models\Organizer;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Reports extends Component
{
    use WithPagination;

    // Filters
    public $startDate;
    public $endDate;
    public $search = '';
    public $gatewayFilter = '';
    public $typeFilter = '';
    public $organizerFilter = '';

    // Stats
    public $totalGtv = 0;
    public $systemRevenue = 0;
    public $subscriptionRevenue = 0;
    public $registrationRevenue = 0;
    public $organizerGtv = 0;
    public $totalDiscount = 0;
    public $subscriptionDiscount = 0;
    public $registrationDiscount = 0;

    protected $queryString = [
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'gatewayFilter' => ['except' => ''],
        'typeFilter' => ['except' => ''],
        'organizerFilter' => ['except' => ''],
    ];

    public function mount()
    {
        if (!$this->startDate) {
            $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        }
        if (!$this->endDate) {
            $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        }
        
        $this->calculateStats();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['startDate', 'endDate', 'gatewayFilter', 'typeFilter', 'organizerFilter'])) {
            $this->calculateStats();
            $this->resetPage();
        }
    }

    public function calculateStats()
    {
        $query = Transaction::where('status', 'paid')
            ->whereDate('created_at', '>=', $this->startDate)
            ->whereDate('created_at', '<=', $this->endDate);

        if ($this->organizerFilter) {
            $query->where('organizer_id', $this->organizerFilter);
        }

        // Clone query for different stats
        $statsQuery = clone $query;
        
        $this->totalGtv = $statsQuery->sum('amount');
        
        $this->systemRevenue = (clone $query)->where('gateway_type', 'system')->sum('amount');
        
        // Split SaaS Revenue
        $this->subscriptionRevenue = (clone $query)
            ->where('gateway_type', 'system')
            ->where('payable_type', 'App\Models\Organizer')
            ->sum('amount');
            
        $this->registrationRevenue = (clone $query)
            ->where('gateway_type', 'system')
            ->where('payable_type', 'App\Models\Registration')
            ->sum('amount');

        $this->organizerGtv = (clone $query)->where('gateway_type', 'organizer')->sum('amount');
        
        // Calculate total discounts using optimized SQL JSON path summation
        $this->totalDiscount = (float) (clone $query)->sum('metadata->discount_amount');
        
        $this->subscriptionDiscount = (float) (clone $query)
            ->where('payable_type', 'App\Models\Organizer')
            ->sum('metadata->discount_amount');
            
        $this->registrationDiscount = (float) (clone $query)
            ->where('payable_type', 'App\Models\Registration')
            ->sum('metadata->discount_amount');
    }

    public function render()
    {
        $query = Transaction::with(['user', 'payable', 'organizer'])
            ->whereDate('created_at', '>=', $this->startDate)
            ->whereDate('created_at', '<=', $this->endDate)
            ->orderBy('created_at', 'desc');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('id', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function($qu) {
                      $qu->where('name', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('organizer', function($qo) {
                      $qo->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->gatewayFilter) {
            $query->where('gateway_type', $this->gatewayFilter);
        }

        if ($this->typeFilter) {
            $query->where('payable_type', $this->typeFilter);
        }

        if ($this->organizerFilter) {
            $query->where('organizer_id', $this->organizerFilter);
        }

        return view('livewire.admin.reports', [
            'transactions' => $query->paginate(15),
            'organizers' => Organizer::all()
        ])->layout('layouts.app');
    }
}
