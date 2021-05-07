<?php

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSent;
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

    public function handle(MessageSent $event)
    {
        $event->subject = 'Notification sent';
        $event->description = 'A notification has been sent';
        activity($event->subject)
            ->withProperties(['properties' => $event->message->getBody()])
            ->log($event->description);
    }
}
