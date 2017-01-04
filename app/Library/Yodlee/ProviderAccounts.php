<?php 

namespace App\Library\Yodlee;

use Auth;
use Carbon\Carbon;

class ProviderAccounts {

	private $yodleeUser; // Object of type YSL User

	/**
     * Create a new account instance.
     *
     * @return void
     */
    public function __construct(User $yodleeUser)
    {
    	$this->yodleeUser = $yodleeUser; // SWOOP: Is this required?
    }


    function addProviderAccounts($params)
    {

    	// Checking if user is active
		if ( $this->yodleeUser->isActive( Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken ) ) {


			$providerAccountId = $params['provider'][0]['id'];

	    	$requestUrl = config('services.yodlee.providers.url'). '/' .$providerAccountId;

	    	$params = json_encode($params, JSON_UNESCAPED_UNICODE);

	    	$responseObj = Utils::httpPostCurl($requestUrl ,$params, Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken);
		
			if ( $responseObj['httpStatus'] == '201' ) {

				return $responseObj['body'];

			} else {

				dd($responseObj);
				// There is no error here!

				$err = array('file' => __FILE__, 'method' => __FUNCTION__, 'event' => 'Adding account to UASFIN'); 
				$error = array_merge($err, $responseObj['error']);
				dd($error);


			}

		} else {

			// Logout user when Yodlee session is inactive
			Auth::Logout();
			return false;
		}
	}

	/**
	 * Get all the provider accounts added by the user. 
	 * This includes the failed and successfully added provider accounts. 
	 * GET /{cobrandName}/v1/providers/providerAccounts
	 * Displayed on /account/status page
	 */
	public function getProviderAccounts() 
	{

		// Checking if user is active
		if ( $this->yodleeUser->isActive( Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken ) ) {

			$request = config('services.yodlee.providerAccounts.url'). '/providerAccounts';
			
			$responseObj = Utils::httpGet($request, Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken);
				
			
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
					'event' => 'Getting all accounts of user', 
					'params' => null, 
				);
				$error = array_merge($err, $responseObj['error']);
				\Log::info(print_r($error, true));
				$msg = 'Yodlee Error ' . $error['code'].' - "'.$error['message'].'"';
				abort(500, $msg);
			}
		
		} else {

			// Logout user when Yodlee session is inactive
			Auth::Logout();
			return false;

		}
	}

	/**
	 * Get the status of add provider account action
	 * GET /{cobrandName}/v1/providers/providerAccounts/{providerAccountId}
	 */
	public function getProviderAccountDetails($providerAccountId) 
	{

		// Checking if user is active
		if ( $this->yodleeUser->isActive( Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken ) ) {

			$request = config('services.yodlee.providerAccounts.url'). '/providerAccounts/'.$providerAccountId;
			
			$responseObj = Utils::httpGet($request, Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken);
				
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
					'event' => 'Getting all accounts of user', 
					'params' => $providerAccountId, 
				);
				$error = array_merge($err, $responseObj['error']);
				\Log::info(print_r($error, true));
				$msg = 'Yodlee Error ' . $error['code'].' - "'.$error['message'].'"';
				abort(500, $msg);
			}
		
		} else {

			// Logout user when Yodlee session is inactive
			Auth::Logout();
			return false;
			
		}
	}

    
	public function deleteProviderAccounts($providerId) 
	{

		// Checking if user is active
		if ( $this->yodleeUser->isActive( Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken ) ) {

			$params = array('providerAccountId' => $providerId);

			$request = config('services.yodlee.providerAccounts.url'). '/providerAccounts/'.$providerId;
			
			$responseObj = Utils::httpDelete($request, $params, Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken);
				
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
					'event' => 'Deleting Provider account', 
					'params' => $providerId, 
				);
				$error = array_merge($err, $responseObj['error']);
				\Log::info(print_r($error, true));
				$msg = 'Yodlee Error ' . $error['code'].' - "'.$error['message'].'"';
				abort(500, $msg);

			}
		
		} else {

			// Logout user when Yodlee session is inactive
			Auth::Logout();
			return false;
			
		}
	}
}