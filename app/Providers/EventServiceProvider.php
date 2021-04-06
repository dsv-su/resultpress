<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        'Illuminate\Auth\Events\Login' => ['App\Listeners\LoginSuccessful'],
        'Illuminate\Notifications\Events\NotificationSent' => ['App\Listeners\SentNotificationSuccessful'],
        'App\Events\PartnerUpdateEvent' => ['App\Listeners\PartnerUpdateListener'],
        'App\Events\ProjectUpdateAcceptEvent' => ['App\Listeners\ProjectUpdateAcceptListener'],
        'App\Events\ProjectUpdateRejectEvent' => ['App\Listeners\ProjectUpdateRejectListener']
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
