<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProjectUpdateAccept extends Mailable
{
    use Queueable, SerializesModels;

    public $subject = 'Project Update Approved';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($projectUpdate, $project)
    {
        $this->details = $projectUpdate;
        $this->project = $project;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.project_update_approved')
                    ->with([
                        'project' => $this->project,
                        'url' => url('/project/'.$this->project->id),
                    ]);
    }
}
