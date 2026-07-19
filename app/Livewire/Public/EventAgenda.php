<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\EventAgenda as AgendaModel;
use Carbon\Carbon;

class EventAgenda extends Component
{
    public $search = '';
    public $month = '';

    public function render()
    {
        // Mengambil agenda yang akan datang atau sesuai filter
        $query = AgendaModel::query();

        // Filter nama (Support JSON Search)
        if ($this->search) {
            $query->where(function($q) {
                // Cari di Bahasa Indonesia ATAU Inggris (Case Insensitive handled by DB usually, but for JSON might need LOWER)
                $q->where('title->id', 'like', '%' . $this->search . '%')
                  ->orWhere('title->en', 'like', '%' . $this->search . '%');
            });
        }

        // Filter Bulan
        if ($this->month) {
            $query->whereMonth('start_time', $this->month);
            // Jika filter bulan aktif, kita mungkin juga ingin melihat history bulan lalu, 
            // jadi kita HAPUS filter 'hari ini ke depan' khusus ketika user memfilter bulan tertentu.
            // Namun jika user ingin melihat agenda bulan X di masa depan saja, logicnya beda.
            // Default behavior biasanya: Filter bulan = Tampilkan semua di bulan itu (termasuk yg lalu).
        } else {
             // Default: Hanya tampilkan hari ini ke depan jika TIDAK sedang memfilter bulan spesifik
             $query->where('start_time', '>=', Carbon::today());
        }

        $agendas = $query->orderBy('start_time', 'asc')
            ->get()
            ->groupBy(function($date) {
                // Jika start_time kosong, kelompokkan ke 'TBA' atau tanggal dummy
                if (!$date->start_time) return 'TBA'; 
                return Carbon::parse($date->start_time)->format('Y-m-d'); 
            });

        return view('livewire.public.event-agenda', [
            'groupedAgendas' => $agendas
        ])->layout('layouts.guest'); // Pastikan layout sesuai dengan tema publik
    }
}