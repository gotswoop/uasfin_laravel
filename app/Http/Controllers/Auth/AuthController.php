<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

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

        // login the Cobrand here
		$cobSession = $cobrand->login();
		
		// \Config::set('services.yodlee.cobrand.sessionToken', $cobrand['session']['cobSession']);
		// $this->cobrandSessionToken = $cobrand['session']['cobSession'];
		\Config::set('services.yodlee.cobrand.sessionToken', $cobSession);
		$this->cobrandSessionToken = $cobSession;
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
    		'firstName.required' => 'Please provide a first name.',
    		'lastName.required' => 'Please provide a last name.',
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
    		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
            return User::create([
            	'panelId' => $data['panelId'],
            	'treatment' => $data['treatment'],
                'firstName' => $data['firstName'],
                'lastName' => $data['lastName'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'suddi' => base64_encode(openssl_encrypt($data['password'], 'aes-256-cbc', $data['email'], 0, $iv)).':'.base64_encode($iv),
                'join_date' => Carbon::now()->toDateTimeString(),
                'last_login_date' => Carbon::now()->toDateTimeString(),
                'regIP' => Request::ip(),
                'access' => 1,
                'yslUserId' => $res['user']['id'],
                'yslUserSessionToken' => $res['user']['session']['userSession'],
                'yslCobrandSessionToken' => $this->cobrandSessionToken,
                'yslUserSessionToken_date' => Carbon::now()->toDateTimeString(),
            ]);
        }

        // return to login register page
        view('auth.register'); // cannot do this . Move all to validation?
        
    }

    // AOXOMOXOA: Overriding trait use Illuminate\Foundation\Auth\AuthenticatesUsers
    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(\Illuminate\Http\Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        $throttles = $this->isUsingThrottlesLoginsTrait();

        if ($throttles && $lockedOut = $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $credentials = $this->getCredentials($request);
        // AOXOMOXOA - This only allows users with access = 1 to login
        $credentials['access'] = 1;

        if (\Auth::guard($this->getGuard())->attempt($credentials, $request->has('remember'))) {
            return $this->handleUserWasAuthenticated($request, $throttles);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        if ($throttles && ! $lockedOut) {
            $this->incrementLoginAttempts($request);
        }

        return $this->sendFailedLoginResponse($request);
    }
}