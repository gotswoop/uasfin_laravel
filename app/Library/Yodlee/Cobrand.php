<?php 

namespace App\Library\Yodlee;

use Auth;
use Carbon\Carbon;

use App\Cobrand as CobrandModel;

class Cobrand {

	/**
     * Logging in the Cobrand and returning the body with Cobrand Id
     *
     * @param  null
     * @return Cobrand Id
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
	        $params = array(
	        	'cobrandLogin' => config('services.yodlee.cobrand.login'), 
	        	'cobrandPassword' => config('services.yodlee.cobrand.password')
	        );

	        // Logging in the the Cobrand
	        $responseObj = Utils::httpPost($requestUrl, $params, null, null);

			if ( $responseObj['httpStatus'] == '200' ) {

				$cobrand = $responseObj['body'];
	 			// return response()->json( [ "foo" => "bar", "error" => [ "messages" => [ "User not found." ] ] ], 404);
	 			\DB::table('cobrand')->insert([
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

    public function getPublicKey() 
    {
		
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

    // Not used

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