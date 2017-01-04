<?php 

namespace App\Library\Yodlee;

use Auth;
use Carbon\Carbon;

class User {

	public function register($data, $cobrandSessionToken) 
	{

		// SWOOP: Check if Cobrand Session is still active

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

			$err = array(
				'datetime' => Carbon::now()->toDateTimeString(),
				'ip' => \Request::ip(),
				'userId' => null, 
				'yslUserId' => null,
				'file' => __FILE__, 
				'method' => __FUNCTION__, 
				'event' => 'User Registration', 
				'params' => json_encode($params), 
			);
			$error = array_merge($err, $responseObj['error']);
			\Log::info(print_r($error, true));
			$msg = 'Yodlee Error ' . $error['code'].' - "'.$error['message'].'"';
			abort(500, $msg);

		}
    }


	public function login($cobrandSessionToken, $email, $password)
    {

    	// SWOOP: Check if Cobrand Session is still active

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

  			Handle error when case is user exists on local db but not on Yodlee.
  			*/

  			// dd($responseObj['error']); 
  			return false;
		}   

		return false;

    }

    /**
    	Using this API to check if a Yodlee user's session is active
     */
    public function isActive($cobrandSessionToken, $userSessionToken)
    {

    	$request = config('services.yodlee.user.detailsUrl');

		$responseObj = Utils::httpGet($request, $cobrandSessionToken, $userSessionToken);

		if ( $responseObj['httpStatus'] != '200' ) {

			return false;

		}

		return true;
    }



    public function logout($cobrandSessionToken, $userSessionToken)
    {

    	// Checking if YSL User session is still active
    	if ( $this->isActive($cobrandSessionToken, $userSessionToken) ) {

	    	$request = config('services.yodlee.user.logoutUrl');

	    	// Logout the YSL User
			$responseObj = Utils::httpPost($request, null, $cobrandSessionToken, $userSessionToken);
			
			if ( $responseObj['httpStatus'] == '204' ) {

				// Logout the local user
				return true;

			} else {

				$err = array(
					'datetime' => Carbon::now()->toDateTimeString(),
					'ip' => \Request::ip(),
					'userId' => Auth::user()->id, 
					'yslUserId' => Auth::user()->yslUserId,
					'file' => __FILE__, 
					'method' => __FUNCTION__, 
					'event' => 'User Logout', 
					'params' => null, 
				);
				$error = array_merge($err, $responseObj['error']);
				\Log::info(print_r($error, true));
				$msg = 'Yodlee Error ' . $error['code'].' - "'.$error['message'].'"';
				abort(500, $msg);

			}

		} else {

			// Don't care if YSL User session has expired. Logout the local user.
			return true;

		}
    }

}