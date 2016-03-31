<?php

namespace App\Listeners;

use App\Events\SomeEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

// SWOOP
use App\User;
use Illuminate\Auth\Events\Attempting;
use GuzzleHttp;
use Carbon\Carbon;

use App\Library\Yodlee\YodleeUser;

class AuthLoginAttemptEventListener
{

    protected $user;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(User $user, YodleeUser $yodleeUser)
    {
        $this->user = $user; // SWOOP
        $this->yodleeUser = $yodleeUser;
    }

    /**
     * Handle the event.
     *
     * @param  SomeEvent  $event
     * @return void
     */
    
    // public function handle(SomeEvent $event)
    public function handle(Attempting $login)
    {

        $cobrandSessionToken = config('services.yodlee.cobrand.sessionToken');

        // Getting user entered credentials
        $email = $login->credentials['email'];
        $password = $login->credentials['password'];
                           
        if ($cobrandSessionToken) { 

            // Logging in the user
            $res = $this->yodleeUser->login($cobrandSessionToken, $email, $password);

            // Saving yodlee cobrandSessionToken, userSessionToken and UserSessionToken create time to users table.
            if(!empty($res['user'])) {
                $userSessionToken = $res['user']['session']['userSession'];
                // fetch the record to update.
                $userToUpdate = $this->user->where('email', '=', $email)->first();
                // Update user table with Yodlee User Sesssion Token
                if ($userToUpdate) {
                     $userToUpdate->update([
                        'yslUserSessionToken' => $res['user']['session']['userSession'],
                        'yslCobrandSessionToken' => $cobrandSessionToken, // temp fix.
                        'yslUserSessionToken_date' => Carbon::now()->toDateTimeString(),
                    ]);
                } else {
                    print "Something terrible happenned"; // SWOOP
                    return false;
                }
                return true;
            }
            
        }
        // SWOOP: Something terrible happenned
        return false;
    }
}
