<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewComment extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct( $comment, $project )
    {
        $this->comment = $comment;
        $this->project = $project;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database']; // 'database' is a Laravel default channel, 'mail' is a custom channel we created in app/Providers/AppServiceProvider.php
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(sprintf('New comment on your project (%s)', $this->project->name))
            ->greeting(sprintf('Hello %s!', $notifiable->name))
            ->line(sprintf('%s has posted a new comment on your project %s', $this->comment->user->name, $this->project->name))
            ->action('View project', $this->project->link)
            ->line('You are receiving this email because you are the partner of this project.');

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

    public function toDatabase()
    {
        return [
            'message' => sprintf('%s has posted a new comment on your project %s', $this->comment->user->name, $this->project->name),
            'link' => $this->project->link,
            'user_id' => $this->comment->user->id,
        ];
    }
}
