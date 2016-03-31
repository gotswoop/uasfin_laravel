<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // SWOOP
       'Illuminate\Auth\Events\Attempting' => [
            'App\Listeners\AuthLoginAttemptEventListener',
        ],
        'Illuminate\Auth\Events\Logout' => [
            'App\Listeners\AuthLogoutEventListener',
        ],
        /*
        // NOT DOING ANYTHING FOR NOW
        'App\Events\SomeEvent' => [
            'App\Listeners\EventListener',
        ],
        'Illuminate\Auth\Events\Login' => [
            'App\Listeners\AuthLoginEventListener',
        ],
        'Illuminate\Auth\Events\Lockout' => [
            'App\Listeners\LogLockout',
        ], 
        */
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
        
    }
}
