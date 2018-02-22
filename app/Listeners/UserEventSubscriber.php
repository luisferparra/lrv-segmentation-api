<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;

class UserEventSubscriber
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
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {

    }

    /**
     * Handle user login events.
     */
    public function onUserLogin($event)
    {

        $user = $event->user;
        $user->last_logged_at = Carbon::now()->format('Y-m-d H:i:s');
        $user->save();

    }

    public function subscribe($events)
    {
        $events->listen(
            'Illuminate\Auth\Events\Login',
            \App\Listeners\UserEventSubscriber::class . '@onUserLogin'
        );

        /* $events->listen(
            'Illuminate\Auth\Events\Logout',
            'App\Listeners\UserEventSubscriber@onUserLogout'
        ); */
    }
}
