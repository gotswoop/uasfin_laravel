<?php 

namespace App\Library\Yodlee;

use Auth;
use Carbon\Carbon;

use App\CobrandSessions as CobrandModel;

class Cobrand {

	/**
     * Logging in the Cobrand and returning the body with Cobrand Id
     *
     * @param  null
     * @return Cobrand Id
     * FROM AuthController constructor
     * YSL URL (POST): https://usyirestmaster.api.yodlee.com/ysl/uscnew/v1/cobrand/login
     */
	public function login() 
	{

		// Checking if cobrand is active (70 mins)
		$cob = CobrandModel::getSession();

		if (array_key_exists('cobSession', $cob)) {
			
			return $cob['cobSession'];

		} else {

			// Fetch cobrand URL from env file
			$requestUrl = config('services.yodlee.cobrand.loginUrl');
				          
	        // Fetch cobrand credentials from env file
	        $cobrand['cobrandLogin'] = config('services.yodlee.cobrand.login');
	        $cobrand['cobrandPassword'] = config('services.yodlee.cobrand.password');
	        $cobrand['locale'] = config('services.yodlee.user.locale');

	        $params = array('cobrand'=>$cobrand);

	        // Logging in the the Cobrand
	        $responseObj = Utils::httpPost($requestUrl, $params, null, null);

	        if ( $responseObj['httpStatus'] == '200' ) {

				$cobrand = $responseObj['body'];
	 			// return response()->json( [ "foo" => "bar", "error" => [ "messages" => [ "User not found." ] ] ], 404);
	 			\DB::table('cobrand_sessions')->insert([
	 				'cobrandId' => $cobrand['cobrandId'],
	 				'applicationId' => $cobrand['applicationId'],
	 				'cobSession' => $cobrand['session']['cobSession'],
	 				'session_time' => date("Y-m-d H:i:s"),
	 			]);
	 			
				// return $responseObj['body'];
				return $cobrand['session']['cobSession'];

			} else {

				$err = array(
					'datetime' => Carbon::now()->toDateTimeString(),
					'ip' => \Request::ip(),
					'userId' => null, 
					'yslUserId' => null,
					'file' => __FILE__, 
					'method' => __FUNCTION__, 
					'event' => 'Cobrand Login', 
					'params' => json_encode($params), 
				);
				$error = array_merge($err, $responseObj['error']);
				\Log::info(print_r($error, true));
				$msg = 'Yodlee Error ' . $error['code'].' - "'.$error['message'].'"';
				abort(500, $msg);

			}
		}
    }

    /*
    * FROM AccountController -> addSubmit
    * YSL URL (POST): https://usyirestmaster.api.yodlee.com/ysl/uscnew/v1/cobrand/publicKey
    */
    public function getPublicKey() 
    {

		// $file = \File::get(base_path('PublicKey.txt'));
		// dd($file);

		$requestUrl = config('services.yodlee.cobrand.publicKeyUrl');

		$responseObj = Utils::httpGet($requestUrl, Auth::user()->yslCobrandSessionToken);

		if ( $responseObj['httpStatus'] == '200' ) {

			return $responseObj['body'];

		} else {

			$err = array(
				'datetime' => Carbon::now()->toDateTimeString(),
				'ip' => \Request::ip(),
				'userId' => Auth::user()->id, 
				'yslUserId' => Auth::user()->yslUserId,
				'file' => __FILE__, 
				'method' => __FUNCTION__, 
				'event' => 'Getting Public Key', 
				'params' => null, 
			);
			$error = array_merge($err, $responseObj['error']);
			\Log::info(print_r($error, true));
			$msg = 'Yodlee Error ' . $error['code'].' - "'.$error['message'].'"';
			abort(500, $msg);

		}

    }

    ########################
    ##	NOT IN USE YET
    ########################
    public function isActive($yslCobrandSessionToken) 
    {
		
		$requestUrl = config('services.yodlee.cobrand.publicKeyUrl');

		$responseObj = Utils::httpGet($requestUrl, $cobrandSessionToken);

		if ( $responseObj['httpStatus'] != '200' ) {

			return false;

		} 

		return true;

    }

    public function logout() 
    {
    	// SWOOP: 
    }

}