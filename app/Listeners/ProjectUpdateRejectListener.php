<?php

namespace App\Listeners;

use App\Events\ProjectUpdateRejectEvent;
use App\Notifications\ProjectUpdateRejectNotification;
use App\Project;
use App\ProjectUpdate;
use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class ProjectUpdateRejectListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(ProjectUpdate $projectUpdate)
    {
        $this->projectupdate = $projectUpdate;
    }

    /**
     * Handle the event.
     *
     * @param  ProjectUpdateRejectEvent  $event
     * @return void
     */
    public function handle(ProjectUpdateRejectEvent $event)
    {
        $user = User::find($event->projectUpdate->user_id);
        $project = Project::find($event->projectUpdate->project_id);
        $message = $event->projectUpdate;
        Mail::to($user)->send(new \App\Mail\ProjectUpdateReject($message, $project));
    }
}
