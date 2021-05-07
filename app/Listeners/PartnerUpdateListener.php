<?php

namespace App\Listeners;

use App\Events\PartnerUpdateEvent;
use App\Notifications\PartnerSentUpdateNotification;
use App\Project;
use App\ProjectOwner;
use App\ProjectUpdate;
use App\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class PartnerUpdateListener
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
     * @param  PartnerUpdateEvent  $event
     * @return void
     */
    public function handle(PartnerUpdateEvent $event)
    {
        $user = User::find($event->projectUpdate->user_id);
        $project = Project::find($event->projectUpdate->project_id);
        $managers = ProjectOwner::with('user')->where('project_id', $event->projectUpdate->project_id)->get();
        $message = $event->projectUpdate;
        foreach($managers as $manager) {
            Mail::to($manager->user)->send(new \App\Mail\PartnerSentUpdate($message, $project, $user));
        }

    }
}
