<?php 

namespace App\Library\Yodlee;

use Auth;

class User {

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
			/*
			SWOOP: Throws an error when a user is not in Yodlee. Let the local DB handle this for now. 
			"code" => "Y002"
  			"message" => "Invalid loginName/password"
  			*/
  			// dd($responseObj['error']); 
  			return false;
		}   

		return false;

    }

    public function logout($cobrandSessionToken, $userSessionToken)
    {

    	$request = config('services.yodlee.user.logoutUrl');

		$responseObj = Utils::httpPost($request, null, $cobrandSessionToken, $userSessionToken);
		
		if ( $responseObj['httpStatus'] == '204' ) {
			return true;
		} else {
			dd($responseObj['error']);
		}
    }

}