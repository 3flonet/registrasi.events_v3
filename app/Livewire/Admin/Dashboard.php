<?php

namespace App\Livewire\Admin;

use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use App\Models\Transaction;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    // Properti untuk Stat Cards
    public $activeEventsCount;
    public $totalRegistrantsCount;
    public $totalUsersCount;
    public $totalOrganizersCount;
    public $totalRevenue = 0;
    public $availableBalance = 0;
    public $isSuperAdmin = false;

    // Properti untuk Grafik & Tabel
    public $registrationChartData;
    public $popularEventsData;
    public $recentRegistrations;
    public $eventPerformanceData;

    public function mount()
    {
        $user = auth()->user();
        $this->isSuperAdmin = $user->isSuperAdmin();

        if ($this->isSuperAdmin) {
            $this->loadSuperAdminStats();
        } else {
            $this->loadOrganizerStats();
        }

        $this->loadCommonAnalytics();
    }

    private function loadSuperAdminStats()
    {
        $this->activeEventsCount = Event::count();
        $this->totalRegistrantsCount = Registration::count();
        $this->totalOrganizersCount = \App\Models\Organizer::count();
        // Total Revenue (Hanya dari Paket Langganan / SaaS Revenue)
        $this->totalRevenue = Transaction::where('status', 'paid')
            ->where('payable_type', \App\Models\Organizer::class)
            ->sum('amount');
        $this->totalUsersCount = User::count();
    }

    private function loadOrganizerStats()
    {
        $this->activeEventsCount = Event::where('end_date', '>=', now())->count();
        $this->totalRegistrantsCount = Registration::count();
        $this->totalUsersCount = User::count();

        // Load Wallet Balance for Organizer
        $wallet = \App\Models\OrganizerWallet::where('organizer_id', auth()->user()->organizer_id)->first();
        $this->availableBalance = $wallet ? $wallet->balance : 0;
    }

    private function loadCommonAnalytics()
    {
        // --- Query untuk Grafik Pendaftaran (30 Hari Terakhir) ---
        $registrationsByDate = Registration::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as count')
        )
            ->where('created_at', '>=', now()->subDays(29))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->pluck('count', 'date');

        $labels = [];
        $data = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = date('d M', strtotime($date));
            $data[] = $registrationsByDate->get($date, 0);
        }

        $this->registrationChartData = [
            'labels' => $labels,
            'data' => $data,
        ];

        // --- Query untuk Event Terpopuler ---
        $popularEvents = Event::withCount('registrations')
            ->orderBy('registrations_count', 'desc')
            ->take(5)
            ->get();

        $this->popularEventsData = [
            'labels' => $popularEvents->map(fn($e) => $e->getTranslation('name', config('app.locale'))),
            'data' => $popularEvents->pluck('registrations_count'),
        ];

        // --- Query untuk Tabel Performa Event ---
        $this->eventPerformanceData = Event::withCount('registrations')
            ->orderBy('registrations_count', 'desc')
            ->take(10)
            ->get();

        // --- Query untuk Aktivitas Terbaru ---
        $this->recentRegistrations = Registration::with(['user', 'event'])->latest()->take(5)->get();
    }

    public function render()
    {
        return view('livewire.admin.dashboard')->layout('layouts.app');
    }
}
