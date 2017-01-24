<?php 

namespace App\Library\Yodlee;

use Auth;
use Carbon\Carbon;

class Provider {

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

	/** 
	 * Search providers based on search string
	 * Returns list of providers indexed by id (providerId)
	 * YSL URL = /providers?name=chase&priority=cobrand
	 * GET https://usyirestmaster.api.yodlee.com/ysl/uscnew/v1/providers?name=chase&priority=cobrand
	 */
    public function searchProviders($searchString) 
	{

		if ( $this->yodleeUser->isActive() ) { // Checking if user is active

			$request = config('services.yodlee.providers.url');

			$queryArgs = array();
			$queryArgs['name']=$searchString;
			$queryArgs['priority']='cobrand'; // other options are: cobrand, suggested (not working), popular (Not working)

			if(count($queryArgs) > 0) {
            	$request = $request.'?'.http_build_query($queryArgs, '', '&');
			}

		   	$responseObj = Utils::httpGet($request, Auth::user()->yslCobrandSessionToken, null);

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
					'event' => 'Searching Providers', 
					'params' => $searchString, 
				);
				$error = array_merge($err, $responseObj['error']);
				\Log::info(print_r($error, true));
				$msg = 'Yodlee Error ' . $error['code'].' - "'.$error['message'].'"';
				abort(500, $msg);

			}
			
		} else {

			return false;

		}
	}

	/** 
	 * Fetch information about the provider such as name, details, login form etc.
	 * Called to populate login screen
	 * YSL URL = /providers/{providerId}
	 * GET https://usyirestmaster.api.yodlee.com/ysl/uscnew/v1/providers/492
	 */
	public function getProviderDetails($providerId) 
	{

		// TODO: Clean input ($providerId)

		if ( $this->yodleeUser->isActive() ) { // Checking if user is active

			$request = config('services.yodlee.providers.url'). '/' .$providerId;

			$responseObj = Utils::httpGet($request, Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken);

			if ( $responseObj['httpStatus'] == '200') {

				if ( array_key_exists('provider', $responseObj['body']) ) {
				
					return $responseObj['body'];

				} else {

					return null;
				}
				
			} else {
				
				$err = array(
					'datetime' => Carbon::now()->toDateTimeString(),
					'ip' => \Request::ip(),
					'userId' => Auth::user()->id, 
					'yslUserId' => Auth::user()->yslUserId,
					'file' => __FILE__, 
					'method' => __FUNCTION__, 
					'event' => 'Fetching provider details', 
					'params' => $providerId, 
				);
				$error = array_merge($err, $responseObj['error']);
				\Log::info(print_r($error, true));
				$msg = 'Yodlee Error ' . $error['code'].' - "'.$error['message'].'"';
				abort(500, $msg);
			}

		} else {

			return false;
		}
	}

    ########################
    ##	NOT IN USE YET
    ########################
	public function refreshAllProviders() // NOT USED YET
	{

		$request = config('services.yodlee.refreshUrl'). '/' .$providerId;

		$responseObj = Utils::httpGet($request, Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken);

		if ( $responseObj['httpStatus'] == '200' ) {
			return $responseObj['body'];
		} else {
			// dd($responseObj['error']);
		}
	}


	public function refreshProvider($providerId)  // NOT CORRECT - NOT USED
	{

		// SWOOP - Clean input ($providerId)
		$request = config('services.yodlee.refreshUrl'). '/' .$providerId;

		$responseObj = Utils::httpGet($request, Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken);
		
		if ( $responseObj['httpStatus'] == '200' ) {

			return $responseObj['body'];

		} else {

			// dd($responseObj['error']);

		}
	}
}
