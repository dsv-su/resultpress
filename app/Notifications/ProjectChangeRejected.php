<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectChangeRejected extends Notification
{
    use Queueable;

    /**
     * @var \App\Project
     */
    protected $project;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct( $project )
    {
        $this->project = $project;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(sprintf('Project `%s` Changes Rejected', $this->project->name))
            ->greeting(sprintf('Hello %s!', $notifiable->name))
            ->line(sprintf('Your changes on project `%s` have been rejected.', $this->project->name))
            ->line('You can view your project by clicking the button below.')
            ->action('View Project', $this->project->link)
            ->line('If you have any questions, please contact us or leave a comment on the project page.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    public function toDatabase( $notifiable )
    {
        return [
            'message' => sprintf('Changes on project `%s` have been rejected.', $this->project->name),
            'link' => $this->project->link,
        ];
    }
}
