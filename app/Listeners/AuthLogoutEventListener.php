<?php

namespace App\Listeners;

use App\Events\SomeEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

// SWOOP
use App\User;
use Illuminate\Auth\Events\Logout;
use Carbon\Carbon;
use Auth;

use App\Library\Yodlee\User as YodleeUser;

class AuthLogoutEventListener
{

    protected $user;
    protected $yodleeUser;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(User $user, YodleeUser $yodleeUser)
    {
        $this->user = $user;
        $this->yodleeUser = $yodleeUser;
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

        	$res = $this->yodleeUser->logout(Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken);
        	
        	if ($res) {
                $userToUpdate = $this->user->where('email', '=', $user->user->email)->first();
                $userToUpdate->update([
                    'yslUserSessionToken' => '',
                    'yslUserSessionToken_date' => '',
                ]);
            }
	    }

        return true;
    }
}
