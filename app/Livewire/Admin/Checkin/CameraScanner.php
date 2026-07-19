<?php

namespace App\Livewire\Admin\Checkin;

use App\Models\Event;
use App\Models\Registration;
use App\Traits\HandlesCheckin;
use Livewire\Component;

class CameraScanner extends Component
{
    use HandlesCheckin;
    public Event $event;
    public $lastScanned = [];
    public $manualUuid = ''; 
    public $totalRegistrants = 0;
    public $totalCheckedIn = 0;
    public $selectedSessionId = '';

    public function mount(Event $event)
    {
        $this->event = $event;
        $this->loadStats();
    }

    public function loadStats()
    {
        $this->totalRegistrants = $this->event->registrations()->count();
        $this->totalCheckedIn = \App\Models\CheckinLog::whereHas('registration', function($q) {
            $q->where('event_id', $this->event->id);
        })->whereBetween('checkin_time', [today()->startOfDay(), today()->endOfDay()])->distinct('registration_id')->count();
    }

    // Method untuk input manual dari handheld scanner
    public function manualCheckIn()
    {
        $this->checkIn($this->manualUuid);
        $this->manualUuid = ''; // Kosongkan input setelah submit
    }

    public function checkIn($inputValue)
    {
        if (empty($inputValue)) {
            return;
        }

        $uuid = basename($inputValue);
        $registration = null;

        // 1. Coba cari Registrasi secara langsung (Scan TIKET)
        $registration = \App\Models\Registration::where('uuid', $uuid)
            ->where('event_id', $this->event->id)
            ->first();

        // 2. Jika tidak ada, mungkin yang di-scan adalah QR Profil Pengguna (Scan PROFIL)
        if (!$registration) {
            $user = \App\Models\User::where('uuid', $uuid)->first();
            if ($user) {
                $registration = \App\Models\Registration::where('user_id', $user->id)
                    ->where('event_id', $this->event->id)
                    ->first();
            }
        }

        // 3. Validasi Akhir
        if (!$registration) {
            $this->lastScanned = ['status' => 'error', 'message' => 'PENGGUNA / TIKET TIDAK DITEMUKAN atau TIDAK TERDAFTAR di event ini.'];
            $this->dispatch('scan-failed');
            return;
        }


        // LOGIKA TERPUSAT: Buat entri baru di checkin_logs & Kirim Notifikasi
        try {
            $sessionVal = $this->selectedSessionId ?: null;
            if ($this->performCheckIn($registration, $this->event, $sessionVal)) {
                 $this->lastScanned = ['status' => 'success', 'message' => 'BERHASIL! Selamat datang, ' . $registration->name];
            } else {
                 $latestLog = $registration->checkinLogs()
                    ->when($sessionVal, fn($q) => $q->where('event_session_id', $sessionVal), fn($q) => $q->whereNull('event_session_id'))
                    ->whereBetween('checkin_time', [today()->startOfDay(), today()->endOfDay()])
                    ->latest('checkin_time')->first();
                 $lastCheckinTime = $latestLog ? $latestLog->checkin_time : now();
                 $this->lastScanned = ['status' => 'warning', 'message' => $registration->name . ' SUDAH CHECK-IN HARI INI pada ' . \Carbon\Carbon::parse($lastCheckinTime)->format('H:i:s')];
                 $this->dispatch('scan-failed');
                 $this->dispatch('scan-finished');
                 return;
            }
        } catch (\Exception $e) {
             $this->lastScanned = ['status' => 'error', 'message' => $e->getMessage()];
             $this->dispatch('scan-failed');
             $this->dispatch('scan-finished');
             return;
        }

        $this->loadStats();
        $this->dispatch('scan-successful');
        $this->dispatch('scan-finished');
    }

    public function render()
    {
        return view('livewire.admin.checkin.camera-scanner')->layout('layouts.app');
    }
}
