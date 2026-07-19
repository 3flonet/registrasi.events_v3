<?php

namespace App\Livewire\Admin\Notifications;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        $this->dispatch('refreshNotifications')->to('admin.notification-bell');
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->dispatch('refreshNotifications')->to('admin.notification-bell');
    }

    public function deleteNotification($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();
        $this->dispatch('refreshNotifications')->to('admin.notification-bell');
    }

    public function render()
    {
        return view('livewire.admin.notifications.index', [
            'allNotifications' => Auth::user()->notifications()->paginate(15)
        ])->layout('layouts.app');
    }
}
