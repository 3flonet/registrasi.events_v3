<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationBell extends Component
{
    public $notifications;
    public $unreadCount = 0;

    public function getListeners()
    {
        $userId = Auth::id();
        return [
            "echo-private:App.Models.User.{$userId},.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated" => 'loadNotifications',
            'refreshNotifications' => 'loadNotifications'
        ];
    }

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $user = Auth::user();
        if (!$user) return;

        $this->notifications = $user->notifications()->latest()->take(5)->get();
        $this->unreadCount = $user->unreadNotifications()->count();
    }

    public function markAsRead($notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            $this->loadNotifications();
        }
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.admin.notification-bell');
    }
}
