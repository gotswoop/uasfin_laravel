<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

// AOXOMOXOA - from vendor/laravel/framework/src/Illuminate/Foundation/Auth/ResetsPasswords.php
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class PasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    // Redirect after a successful password change
    protected $redirectTo = 'account/dashboard';

    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    // AOXOMOXOA: Overriding trait vendor/laravel/framework/src/Illuminate/Foundation/Auth/ResetsPasswords
    // http://gitamin.com/Laravel/framework/raw/9909728897800de6452ebb562e665e541b6e8251/src/Illuminate/Foundation/Auth/ResetsPasswords.php

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function reset(Request $request)
    {
		// $this->validate($request, $this->getResetValidationRules());
        $this->validate(
            $request,
            $this->getResetValidationRules(),
            $this->getResetValidationMessages(),
            $this->getResetValidationCustomAttributes()
        );

        $credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );

        $broker = $this->getBroker();

        $response = Password::broker($broker)->reset($credentials, function ($user, $password) {
            $this->resetPassword($user, $password);
        });

        switch ($response) {
            case Password::PASSWORD_RESET:
                return $this->getResetSuccessResponse($response);

            default:
                return $this->getResetFailureResponse($request, $response);
        }
    }

    /**
     * Get the password reset validation rules.
     * 
     * AOXOMOXOA: Overriding vendor/laravel/framework/src/Illuminate/Foundation/Auth/ResetsPasswords.php
     * 
     * @return array
     */
    protected function getResetValidationRules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => array(
            	'required',
            	'min:8',
    			'regex:/^.*(?!.*(\]|\.))(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[!@#$()%^&*]).*$/',
    			'confirmed'
    		),
        ];
    }
   
    /**
     * Get the password reset validation messages.
     *
     * @return array
     */
    protected function getResetValidationMessages()
    {
        return [
    		'password.regex' => 'Password must be at least 8 characters long and contain at least one upper case letter, one number and any of these special characters !@#$%^&*() and cannot contain ] and .',
   		];
   		
    }

    /**
     * Get the password reset validation custom attributes.
     *
     * @return array
     */
    protected function getResetValidationCustomAttributes()
    {
        return [];
    }

}
