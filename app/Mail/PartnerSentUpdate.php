<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PartnerSentUpdate extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($projectUpdate, $project, $user)
    {
        $this->details = $projectUpdate;
        $this->project = $project;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.partner_sent_update')
                    ->with([
                        'project' => $this->project,
                        'user' => $this->user,
                        'url' => url('/project/'.$this->project->id),
                    ]);
    }
}
