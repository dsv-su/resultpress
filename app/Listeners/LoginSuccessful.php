<?php

namespace App\Listeners;

use IlluminateAuthEventsLogin;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Session;
use Spatie\Activitylog\Models\Activity;

class LoginSuccessful
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  IlluminateAuthEventsLogin  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $event->subject = 'login';
        $event->description = 'Login successful';

        Session::flash('login-success', 'Welcome' . $event->user->name);
        activity($event->subject)
            ->by($event->user)
            ->withProperties(['Logged_in' => $event->user->name])
            ->log($event->description);

    }
}
