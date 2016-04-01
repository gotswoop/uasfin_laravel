<?php

// SWOOP

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

use GuzzleHttp;
use Carbon\Carbon;
use Request;

use App\Library\Yodlee\Cobrand;
use App\Library\Yodlee\User as YodleeUser;

class AuthController extends Controller
{

	private $yodleeUser; // Object
	private $cobrandSessionToken; // variable
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/account/dashboard';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct(Cobrand $cobrand, YodleeUser $yodleeUser)
    {
        $this->middleware('guest', ['except' => 'logout']);

        $this->yodleeUser = $yodleeUser;

        // login cobrand here
		$cobrand = $cobrand->login();
		
		\Config::set('services.yodlee.cobrand.sessionToken', $cobrand['session']['cobSession']);

		$this->cobrandSessionToken = $cobrand['session']['cobSession'];
    }

	/*
	// SWOOP
    protected function getFailedLoginMessage()
	{
    	return 'what you want here.';
	}
	*/

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {

    	/* 
    	Password requirements from Yodlee: Password must be at 
    	least 8 characters long and contain at least one upper 
    	case letter, one number and any of these special characters 
    	!@#$%^&*()].
    	
    	Swaroop: Documentation is wrong. "]" and "." are actually NOT allowed
    	*/

		$messages = [
    		'firstName.required' => 'Pleaes provide a first name.',
    		'lastName.required' => 'Pleaes provide a last name.',
    		'email.required' => 'Please provide a valid email.',
    		'password.regex' => 'Password must be at least 8 characters long and contain at least one upper case letter, one number and any of these special characters !@#$%^&*() and cannot contain ] and .',
    		'invite_code.regex' => 'Please enter a valid invitation code',
   		];
   		
    	$rules = [
            'firstName' => 'required|max:255',
            'lastName' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => array(
            	'required',
            	'min:8',
    			'regex:/^.*(?!.*(\]|\.))(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[!@#$()%^&*]).*$/',
    			'confirmed'
    		),
    		'invite_code' => array('required', 'regex:/^CESR$/'),
        ];

    	return Validator::make($data, $rules, $messages);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {

    	if ($this->cobrandSessionToken) {

            // Registering the user
    		$res = $this->yodleeUser->register($data, $this->cobrandSessionToken);

    		// Saving yodlee cobrandSessionToken, userSessionToken and UserSessionToken create time to users table.
                    
            return User::create([
                'firstName' => $data['firstName'],
                'lastName' => $data['lastName'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'suddi' => $data['password'],
                'join_date' => Carbon::now()->toDateTimeString(),
                'last_login_date' => Carbon::now()->toDateTimeString(),
                'regIP' => Request::ip(),
                'yslUserId' => $res['user']['id'],
                'yslUserSessionToken' => $res['user']['session']['userSession'],
                'yslCobrandSessionToken' => $this->cobrandSessionToken,
                'yslUserSessionToken_date' => Carbon::now()->toDateTimeString(),
            ]);
        }
        // return to login register page
        view('auth.register'); // cannot do this . Move all to validation?
        
    }
}