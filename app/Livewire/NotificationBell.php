<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationBell extends Component
{
    public $notifications = [];
    public $unreadCount = 0;
    public $isOpen = false;

    public function mount()
    {
        $this->fetchNotifications();
    }

    public function fetchNotifications()
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $this->notifications = $user->notifications()->take(5)->get();
            $this->unreadCount = $user->unreadNotifications()->count();
        }
    }

    public function markAsRead(string $notificationId)
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $notification = $user->notifications()->find($notificationId);
            if ($notification) {
                $notification->markAsRead();
                $this->fetchNotifications();
            }
        }
    }

    public function markAllAsRead()
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $user->unreadNotifications->markAsRead();
            $this->fetchNotifications();
            $this->isOpen = false;
        }
    }
    
    public function toggleDropdown()
    {
        $this->isOpen = !$this->isOpen;
        if ($this->isOpen) {
            $this->fetchNotifications();
        }
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}
