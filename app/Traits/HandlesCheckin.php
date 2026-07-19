<?php

namespace App\Traits;

use App\Models\Registration;
use App\Models\Event;
use App\Models\EventEmailTemplate;
use App\Services\WhatsAppService;
use App\Mail\DynamicBroadcastMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

trait HandlesCheckin
{
    /**
     * Memproses check-in peserta dan mengirimkan notifikasi jika template tersedia.
     */
    public function performCheckIn(Registration $registration, Event $event, $sessionId = null)
    {
        // 1. Cek apakah check-in untuk sesi atau event utama
        if ($sessionId) {
            // Cek apakah peserta terdaftar di sesi tersebut
            $isRegisteredForSession = $registration->sessions()->where('event_sessions.id', $sessionId)->exists();
            if (!$isRegisteredForSession) {
                throw new \Exception('Akses Ditolak: Peserta tidak terdaftar di sesi ini!');
            }

            $hasCheckedInToday = $registration->checkinLogs()
                ->where('event_session_id', $sessionId)
                ->whereBetween('checkin_time', [today()->startOfDay(), today()->endOfDay()])
                ->exists();
            
            if (!$hasCheckedInToday) {
                $registration->checkinLogs()->create([
                    'event_session_id' => $sessionId,
                    'checkin_time' => now()
                ]);
                return true;
            }
            return false;
        }

        // Default event-wide check-in
        $hasCheckedInToday = $registration->checkinLogs()
            ->whereNull('event_session_id')
            ->whereBetween('checkin_time', [today()->startOfDay(), today()->endOfDay()])
            ->exists();
        
        if (!$hasCheckedInToday) {
            // 2. Buat log check-in dan update timestamp utama
            $registration->checkinLogs()->create(['checkin_time' => now()]);
            $registration->update(['checked_in_at' => now()]);
            
            // 3. Kirim Notifikasi (Hanya jika template dikonfigurasi)
            $this->sendCheckinNotifications($registration, $event);
            
            return true;
        }

        return false;
    }

    /**
     * Mencari template auto_checkin dan mengirimkannya via WA/Email.
     * Tidak ada pengiriman jika template tidak ditemukan.
     */
    protected function sendCheckinNotifications(Registration $registration, Event $event)
    {
        $template = null;

        // PRIORITAS 1: Gunakan template yang dipilih secara manual di Settings Event (jika ada)
        if (!empty($event->checkin_template_id)) {
            $template = EventEmailTemplate::find($event->checkin_template_id);
        }

        // PRIORITAS 2: Jika tidak ada pilihan manual, cari template dengan kategori auto_checkin
        if (!$template) {
            $template = EventEmailTemplate::where('category', 'auto_checkin')
                ->where(function($q) use ($event) {
                    $q->where('event_id', $event->id)
                      ->orWhereNull('event_id');
                })
                ->orderBy('event_id', 'desc') // Prioritaskan template spesifik event
                ->first();
        }

        // Jika tetap tidak ada template, biarkan proses check-in berlanjut tanpa pengiriman
        if (!$template) {
            return;
        }

        // --- DISPATCH JOB KE BACKGROUND QUEUE ---
        // Kita pindahkan proses pengiriman yang berat ke background agar scanner tidak lambat
        \App\Jobs\SendCheckinNotificationJob::dispatch($registration, $event, $template->id);
    }
}
