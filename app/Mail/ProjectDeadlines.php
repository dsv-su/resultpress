<?php

namespace App\Mail;

use App\Project;
use App\ProjectReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProjectDeadlines extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Project $project, ProjectReminder $projectReminder, ProjectReminder $delayed)
    {
        $this->details = $project;
        $this->project_reminder = $projectReminder;
        $this->delayed_project_reminder = $delayed;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Get all activites from project
        $this->activities = $this->details->activities()->get();
        return $this->text('email.projectdeadlines')
            ->with([
                'details' => $this->details,
                'activities' => $this->activities,
                'project_reminder' => $this->project_reminder,
                'delayed_project_reminder' => $this->delayed_project_reminder,
            ]);

    }
}
