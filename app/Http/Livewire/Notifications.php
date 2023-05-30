<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Notifications extends Component
{
    public $notifications;

    public function mount()
    {
        $this->notifications = Auth::user()->notifications->take(5);
    }

    public function render()
    {
        return view('livewire.notifications', [
            'notifications' => $this->notifications,
        ]);
    }

    public function markAsRead($notificationId)
    {
        $notification = Auth::user()->notifications->where('id', $notificationId)->first();
        if ($notification) {
            $notification->markAsRead();
            $this->notifications = Auth::user()->notifications;
        }
    }
}
