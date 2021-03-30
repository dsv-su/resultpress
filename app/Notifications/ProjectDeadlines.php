<?php

namespace App\Notifications;

use App\Project;
use App\ProjectReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectDeadlines extends Notification
{
    use Queueable;
    protected $activity_remind;
    /**
     * Create a new notification instance.
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
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // Get all activites from project
        $this->activities = $this->details->activities()->get();
        // Create new notification with details
        $message = new MailMessage;
        $message->line( $this->project_reminder->name)
        ->line('Regarding Project: '.$this->details->name.', '.$this->details->start->format('d-m-Y').' - '. $this->details->end->format('d-m-Y'))
        ->line('You have an upcoming reporting deadline at '.$this->project_reminder->set->format('d-m-Y'))
        ->line('The report should cover the following activities:');
        // Add activites covered by current deadline
        foreach($this->activities as $this->activity) {
                //Check that activities are not marked completed or archived
                if($this->activity->status() != 4 and $this->activity->status() != 5) {
                $reminder_message = 'Activity ' . ': '. $this->activity->title. ' - ends '. $this->activity->end->format('d-m-Y');
                $message->line(nl2br($reminder_message));
            }
        }
        //If there exist a earlier deadline
        if($this->delayed_project_reminder->reminder == true){
            $xloop = 0;
            foreach($this->activities as $this->activity) {
                //If activity is flagged as delayed
                if($this->activity->status() == 3) {
                    if($xloop == 0) {
                        $message->line('Additionally, these activities are delayed and should be reported on ASAP:');
                        $xloop++;
                    }
                    $reminder_message = 'Activity '. ': '. $this->activity->title. ' - ended '. $this->activity->end->format('d-m-Y') . ', original reporting deadline was '. $this->delayed_project_reminder->set->format('d-m-Y');
                    $message->line(nl2br($reminder_message));
                }
            }
        }

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
