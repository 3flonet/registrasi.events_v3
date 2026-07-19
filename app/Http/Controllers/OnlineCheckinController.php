<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use App\Traits\HandlesCheckin;
use Illuminate\Http\Request;

class OnlineCheckinController extends Controller
{
    use HandlesCheckin;

    // Method untuk menampilkan halaman form check-in
    public function show($event)
    {
        $event = Event::withoutGlobalScope('organizer')->where('slug', $event)->firstOrFail();

        // Pastikan hanya event online/hybrid yang bisa diakses
        if ($event->type === 'offline') {
            abort(404);
        }
        return view('online-checkin.show', ['event' => $event]);
    }

    // Method untuk memproses form check-in
    public function store(Request $request, $event)
    {
        $event = Event::withoutGlobalScope('organizer')->where('slug', $event)->firstOrFail();
        $request->validate(['email' => 'required|email']);

        // 2. Cari pendaftaran berdasarkan event dan email
        $registration = Registration::withoutGlobalScope('organizer')
            ->where('event_id', $event->id)
            ->where('email', $request->email)
            ->first();

        if (!$registration) {
            return back()->with('error', 'This email is not registered for this event.');
        }

        // 3. Pastikan pendaftar ini adalah peserta online (jika eventnya hybrid)
        if ($event->type === 'hybrid' && $registration->attendance_type !== 'online') {
            return back()->with('error', 'This registration is for offline attendance.');
        }

        // 4. Proses Check-in menggunakan Trait (Mendukung multi-day & Notifikasi otomatis)
        if ($this->performCheckIn($registration, $event)) {
             // 6. Arahkan ke halaman sukses
             return redirect()->route('online.checkin.success', $registration->uuid);
        } else {
             $lastCheckin = $registration->checkinLogs()->whereDate('checkin_time', today())->latest()->first();
             return back()->with('error', 'You have already checked in today at ' . $lastCheckin->checkin_time->format('H:i T'));
        }
    }

    // Method untuk menampilkan halaman sukses
    public function success($registration)
    {
        $registration = Registration::withoutGlobalScope('organizer')->where('uuid', $registration)->firstOrFail();
        return view('online-checkin.success', ['registration' => $registration]);
    }
}
