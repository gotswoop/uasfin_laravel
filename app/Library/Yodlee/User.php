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

    public function passwordReset($user, $cobrandSessionToken, $loginName, $newPassword) {

    	// Getting User's password reset token
        $userToken = $this->getToken($cobrandSessionToken, $loginName);

        if ($userToken) {
        	$res = $this->resetPasswordWithToken($cobrandSessionToken, $user, $userToken, $loginName, $newPassword);
			if ($res === true) {
				return true;
			}
        }
        return false;

    }

    public function passwordUpdate($cobrandSessionToken, $userSessionToken) {

    	// todo
    	
    }
    /**
    	Using this API to check if a Yodlee user's session is active
     */
    public function isActive($cobrandSessionToken, $userSessionToken)
    {

    	$request = config('services.yodlee.user.detailsUrl');

		$responseObj = Utils::httpGet($request, $cobrandSessionToken, $userSessionToken);

		if ( $responseObj['httpStatus'] == '200' ) {

			return true;

		}

		return false;
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

    private function getToken($cobrandSessionToken, $loginName) {

    	// TODO - get from config
    	$request = 'https://usyirestmaster.api.yodlee.com/ysl/uscnew/v1/user/credentials/token';

		$queryArgs = array();
		$queryArgs['loginName']=$loginName;

		if(count($queryArgs) > 0) {
        	$request = $request.'?'.http_build_query($queryArgs, '', '&');
		}

	   	$responseObj = Utils::httpGet($request, $cobrandSessionToken, null);

		if ( $responseObj['httpStatus'] == '200' ) {
		
			return $responseObj['body']['token'];

		} else {

			$err = array(
				'datetime' => Carbon::now()->toDateTimeString(),
				'ip' => \Request::ip(),
				'userId' => Auth::user()->id, 
				'yslUserId' => Auth::user()->yslUserId,	
				'file' => __FILE__, 
				'method' => __FUNCTION__, 
				'event' => 'Searching Providers', 
				'params' => $searchString, 
			);
			$error = array_merge($err, $responseObj['error']);
			\Log::info(print_r($error, true));
			$msg = 'Yodlee Error ' . $error['code'].' - "'.$error['message'].'"';
			abort(500, $msg);

		}
    }

    private function resetPasswordWithToken($cobrandSessionToken, $user, $userToken, $loginName, $newPassword) {

    	// TODO - get from config
    	$requestUrl = 'https://usyirestmaster.api.yodlee.com/ysl/uscnew/v1/user/credentials';

    	$payload = array("loginName"=>$loginName, "token"=>$userToken, "newPassword"=> $newPassword);

		$params = array('user'=>$payload);
		$params = json_encode($params, JSON_UNESCAPED_UNICODE);
			
        $responseObj = Utils::httpPostCurl($requestUrl ,$params, $cobrandSessionToken, null);

		if ( $responseObj['httpStatus'] == '204' ) {

			return true;

		} else {

			$err = array(
				'datetime' => Carbon::now()->toDateTimeString(),
				'ip' => \Request::ip(),
				'userId' => $user->id, 
				'yslUserId' => $user->yslUserId,	
				'file' => __FILE__, 
				'method' => __FUNCTION__, 
				'event' => 'Resetting Password with token', 
				'params' => '', 
			);
			$error = array_merge($err, $responseObj); // THIS IS DIFF
			\Log::info(print_r($error, true));
			// $msg = 'Yodlee Error ' . $error['code'].' - "'.$error['message'].'"';
			// abort(500, $msg);
			return false;

		}
	}

}