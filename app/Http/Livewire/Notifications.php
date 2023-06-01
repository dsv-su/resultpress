<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Notifications extends Component
{
    private $notifications;

    public function mount()
    {
        $this->notifications = Auth::user()->notifications()->where(
            function ($query) {
                $query->where('read_at', null)
                    ->orWhere(function ($query) {
                        $query->where('read_at', '!=', null)
                            ->where('created_at', '>', now()->subDays(30));
                    });
            }
        )->limit(50)->get();
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
