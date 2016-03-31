<?php

namespace App\Listeners;

use App\Events\SomeEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

// SWOOP
use App\User;
use Illuminate\Auth\Events\Logout;
use GuzzleHttp;
use Carbon\Carbon;
use Auth;

class AuthLogoutEventListener
{

    protected $user;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user; // SWOOP
    }

    /**
     * Handle the event.
     *
     * @param  SomeEvent  $event
     * @return void
     */
    
    // public function handle(SomeEvent $event)
    public function handle(Logout $user)
    {

        if(Auth::check()) {
            $client = new GuzzleHttp\Client(['http_errors' => false]);
            
            $auth = '{cobSession='.Auth::user()->yslCobrandSessionToken .', userSession='.Auth::user()->yslUserSessionToken.'}';
            $res = $client->request('POST', config('services.yodlee.user.logoutUrl'), [
                'headers' => [ 'Authorization' => $auth ],
            ]);

            if ($res->getStatusCode() == "204") {
                $userToUpdate = $this->user->where('email', '=', $user->user->email)->first();
                $userToUpdate->update([
                    'yslUserSessionToken' => null,
                    'yslUserSessionToken_date' => null,
                ]);
            }
        }

        return true;
    }
}
