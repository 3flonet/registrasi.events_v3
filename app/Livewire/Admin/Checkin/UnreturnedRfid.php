<?php

namespace App\Livewire\Admin\Checkin;

use App\Models\User;
use App\Models\Event;
use Livewire\Component;
use Livewire\WithPagination;

class UnreturnedRfid extends Component
{
    use WithPagination;

    public $search = '';
    public $filterEvent = '';
    public $selectedUserForReturn = null;

    protected $queryString = ['search', 'filterEvent'];

    public function prepareReturn($userId)
    {
        $this->selectedUserForReturn = User::find($userId);
        $this->dispatch('open-manual-return-modal');
    }

    public function cancelReturn()
    {
        $this->selectedUserForReturn = null;
    }

    public function returnAllByEvent()
    {
        if (!$this->filterEvent) {
            session()->flash('error', "Silakan pilih event terlebih dahulu untuk melakukan pengembalian masal.");
            return;
        }

        $event = Event::find($this->filterEvent);
        if (!$event) return;

        // Cari user yang punya RFID dan terdaftar di event ini
        $usersToClear = User::whereNotNull('rfid_tag')
            ->whereHas('registrations', function($q) {
                $q->where('event_id', $this->filterEvent);
            });

        $count = $usersToClear->count();
        
        if ($count === 0) {
            session()->flash('info', "Tidak ada RFID yang perlu dikembalikan untuk event ini.");
            return;
        }

        $usersToClear->update(['rfid_tag' => null]);

        session()->flash('success', "Berhasil mengembalikan {$count} kartu RFID secara masal untuk event: {$event->name}.");
        $this->dispatch('close-bulk-return-modal');
    }

    public function returnRfid()
    {
        if ($this->selectedUserForReturn) {
            $user = $this->selectedUserForReturn;
            $oldTag = $user->rfid_tag;
            $user->update(['rfid_tag' => null]);
            session()->flash('success', "RFID Tag {$oldTag} milik {$user->name} berhasil dikembalikan.");
            
            $this->selectedUserForReturn = null;
            $this->dispatch('close-manual-return-modal');
        }
    }

    public function render()
    {
        $query = User::whereNotNull('rfid_tag')
            ->when($this->search, function($q) {
                $q->where(function($inner) {
                    $inner->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('email', 'like', '%' . $this->search . '%')
                          ->orWhere('rfid_tag', 'like', '%' . $this->search . '%');
                });
            });

        // Jika ada filter event, cari pendaftar di event tersebut yang punya RFID
        // Karena rfid_tag ada di tabel users, kita join ke registrations
        if ($this->filterEvent) {
            $query->whereHas('registrations', function($q) {
                $q->where('event_id', $this->filterEvent);
            });
        }

        $users = $query->with(['registrations' => function($q) {
            $q->with('event')->latest();
        }])->paginate(10);

        $events = Event::orderBy('start_date', 'desc')->get();

        return view('livewire.admin.checkin.unreturned-rfid', [
            'users' => $users,
            'events' => $events
        ])->layout('layouts.app');
    }
}
