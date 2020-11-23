<?php

namespace App\Listeners;

use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\Activitylog\Models\Activity;

class SentNotificationSuccessful
{
    /**
     * Create the event listener.
     *
     * @return void
     */

    public function handle(NotificationSent $event)
    {
        $event->subject = 'Notification sent';
        $event->description = 'A notification has been sent to: '.$event->notifiable->routes['mail'];
        activity($event->subject)
            ->withProperties(['properties' => $event->notification->details])
            ->log($event->description);
    }
}
