<?php 

namespace App\Library\Yodlee;

use Auth;

class YodleeUser {

	public function register($data, $cobrandSessionToken) 
	{

		$request = config('services.yodlee.user.registerUrl');

		$params = array('registerParam' => '{ 
	        "user": {
	            "loginName": "'.$data['email'].'",
	            "password": "'.$data['password'].'",
	            "email": "'.$data['email'].'",
	        	"name": {
	            	"first" : "'.$data['firstName'].'",
	                "last": "'.$data['lastName'].'"
				},
	            "preferences": {
	                "currency": "'.config('services.yodlee.user.currenyPreference').'",
	                "timeZone": "'.config('services.yodlee.user.timezonePreference').'",
	                "dateFormat": "'.config('services.yodlee.user.dateFormatPreference').'"
	            }
			}
        }');

		$responseObj = Utils::httpPost($request, $params, $cobrandSessionToken, null);

		if ( $responseObj['httpStatus'] == '200' ) {
			return $responseObj['body'];
		} else {
			dd($responseObj['error']);
		}
    }


	public function login($cobrandSessionToken, $email, $password)
    {

		$request = config('services.yodlee.user.loginUrl');

		$params = array('loginName' => $email, 'password' => $password );

		$responseObj = Utils::httpPost($request, $params, $cobrandSessionToken, null);

		if ( $responseObj['httpStatus'] == '200' ) {
			return $responseObj['body'];
		} else {
			// dd($responseObj['error']); SWOOP
			return;
		}       

    }

    public function logout()
    {




    }

}