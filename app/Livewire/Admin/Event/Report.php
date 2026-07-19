<?php

namespace App\Livewire\Admin\Event;

use App\Models\Event;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Invitation;
use App\Exports\PendingInvitationsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AllInvitationsExport;

use Livewire\WithPagination;

class Report extends Component
{
    use \App\Traits\HandlesEventReporting, WithPagination;

    public Event $event;
    public bool $isLiveMode = true;
    // public $participants = []; // Moved to render() for better performance and reliability
    public $customFields = [];
    
    // Invitation filtering
    public $invitationSearch = '';
    public $invitationStatus = 'all';

    /**
     * Method mount() dieksekusi saat komponen dimuat.
     * Ia menerima event dari URL dan memanggil kalkulasi statistik.
     */
    public function mount(Event $event)
    {
        // 1. Simpan event yang sedang dilihat
        $this->event = $event;

        // 2. Auto-disable Live Mode if event already ended
        if ($this->event->end_date && $this->event->end_date->isPast()) {
            $this->isLiveMode = false;
        }

        // 3. Ambil data kustom fields untuk header tabel
        if ($this->event->inquiryForm) {
            $this->customFields = $this->event->inquiryForm->fields;
        }

        // 4. Hitung semua data statistik
        $this->calculateStats();
        
        // $this->loadParticipants(); // No longer needed in mount
    }

    public function loadParticipants()
    {
        $this->participants = $this->event->registrations()
            ->with(['submission', 'ticketTier'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Menghapus semua log check-in untuk event ini pada tanggal tertentu.
     */
    public function deleteLogsForDate($dateString)
    {
        // 1. Validasi tanggal (untuk keamanan)
        try {
            $date = \Carbon\Carbon::parse($dateString)->toDateString();
        } catch (\Exception $e) {
            // Jika tanggal tidak valid, jangan lakukan apa-apa
            return;
        }

        // 2. Hapus log yang sesuai
        // Kita gunakan relasi `checkinLogs()` untuk memastikan
        // kita hanya menghapus log milik event ini.
        $this->event->checkinLogs()
            ->whereDate('checkin_time', $date)
            ->delete();

        // 3. Hitung ulang statistik
        // Ini adalah langkah PENTING. Setelah data dihapus,
        // kita panggil lagi calculateStats() agar semua angka
        // (Total Hadir, Rincian Harian) diperbarui.
        $this->calculateStats();

        // 4. [OPSIONAL] Kirim pesan sukses ke tampilan
        session()->flash('message', 'Data check-in untuk tanggal ' . \Carbon\Carbon::parse($date)->format('d M Y') . ' berhasil dihapus.');
    }

    public function exportPending()
    {
        $date = now()->format('Y-m-d');
        return Excel::download(
            new PendingInvitationsExport($this->event->id),
            "pending-invitations-{$this->event->slug}-{$date}.xlsx"
        );
    }

    public function exportAll()
    {
        $date = now()->format('Y-m-d');
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\EventParticipantsExport($this->event->id),
            "rekap-partisipan-{$this->event->slug}-{$date}.xlsx"
        );
    }

    public function exportAllInvitations()
    {
        $date = now()->format('Y-m-d');
        return Excel::download(
            new AllInvitationsExport($this->event->id),
            "all-invitations-{$this->event->slug}-{$date}.xlsx"
        );
    }

    /**
     * Merender tampilan (view) untuk laporan.
     */

    public function render()
    {
        $invitations = Invitation::where('event_id', $this->event->id)
            ->when($this->invitationSearch, function ($q) {
                $q->where(function($q) {
                    $q->where('name', 'like', '%' . $this->invitationSearch . '%')
                      ->orWhere('email', 'like', '%' . $this->invitationSearch . '%')
                      ->orWhere('company', 'like', '%' . $this->invitationSearch . '%');
                });
            })
            ->when($this->invitationStatus !== 'all', function ($q) {
                $q->where('status', $this->invitationStatus);
            })
            ->latest()
            ->paginate(15, pageName: 'invitation-page');

        $participants = $this->event->registrations()
            ->with(['submission', 'ticketTier'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.admin.event.report', [
            'invitations' => $invitations,
            'participants' => $participants
        ])->layout('layouts.app');
    }
}
