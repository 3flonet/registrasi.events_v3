<?php

namespace App\Livewire\Public;

use App\Models\Registration;
use Livewire\Component;
use Livewire\Attributes\Layout;

class OrderCancelled extends Component
{
    public $registration;

    public function mount($registration_uuid)
    {
        $this->registration = Registration::withoutGlobalScope('organizer')
            ->where('uuid', $registration_uuid)
            ->firstOrFail();

        // Cek keamanan: Jika statusnya bukan 'cancelled', lempar balik ke invoice
        if ($this->registration->status !== 'cancelled') {
            return redirect()->route('invoice.show', $this->registration->uuid);
        }
    }

    public function render()
    {
        // Kita gunakan layout 'blank' agar tampilan bersih
        return view('livewire.public.order-cancelled')
            ->layout('layouts.blank', [
                'title' => 'Pesanan Dibatalkan'
            ]);
    }
}
