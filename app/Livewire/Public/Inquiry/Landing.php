<?php

namespace App\Livewire\Public\Inquiry;

use App\Models\InquiryForm;
use Livewire\Component;

class Landing extends Component
{
    public function render()
    {
        return view('livewire.public.inquiry.landing', [
            'forms' => InquiryForm::whereNotNull('event_agenda_id')
                ->orWhere('has_categories', true)
                ->get()
        ])->layout('layouts.guest');
    }
}
