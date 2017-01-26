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
    	$this->yodleeUser = $yodleeUser;
    }

    /*	
    * YSL URL (POST CURL): https://usyirestmaster.api.yodlee.com/ysl/uscnew/v1/providerAccount/?providerId=643
    * Arguments: Array with providerId and Login Form with encrypted credentials
    * Returns (on success): Array with providerAccountId, providerId, Account addition Status, and error codes or messages if any
    */
    function addProviderAccounts($params)
    {

    	if ( $this->yodleeUser->isActive() ) { // Checking if user is active
		
			$requestUrl = config('services.yodlee.providerAccounts.url');

			$queryArgs = array();
			$queryArgs['providerId'] = $params['id'];
		
			if(count($queryArgs) > 0) {
            	$requestUrl = $requestUrl.'?'.http_build_query($queryArgs, '', '&');
			}

			// TODO: WHAT and WHY?
			$params = $params['loginForm'];
			$params = array('loginForm'=>$params);
			
			$responseObj = Utils::httpPost($requestUrl, $params, Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken);
			
			if ( $responseObj['httpStatus'] == '201' ) {

				return $responseObj['body'];

			} else {

				$err = array(
					'datetime' => Carbon::now()->toDateTimeString(),
					'ip' => \Request::ip(),
					'userId' => Auth::user()->id, 
					'yslUserId' => Auth::user()->yslUserId,
					'file' => __FILE__, 
					'method' => __FUNCTION__, 
					'event' => 'Adding Provider Account', 
					'params' => json_encode($params), 
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

	/*	
    * Update providerAccount. 
    * Used for MFA and login retry (not used yet)
    * YSL URL (PUT CURL): https://usyirestmaster.api.yodlee.com/ysl/uscnew/v1/providerAccount/?providerAccountId=1234566
    * Arguments: Array with providerAccountId and Login Form / MFA form with encrypted credentials
    * Returns (on success): Array with providerAccountId (same as input), providerId, Account update status, and error codes or messages if any
    */
    function updateProviderAccounts($params)
    {

    	if ( $this->yodleeUser->isActive() ) { // Checking if user is active
		
			$requestUrl = config('services.yodlee.providerAccounts.url');

			$queryArgs = array();
			$queryArgs['providerAccountIds'] = $params['providerAccountId'];
		
			if(count($queryArgs) > 0) {
            	$requestUrl = $requestUrl.'?'.http_build_query($queryArgs, '', '&');
			}

			$params = $params['params'];
			
			$responseObj = Utils::httpPut($requestUrl, $params, Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken);

			if ( $responseObj['httpStatus'] == '200' ) {

				$res = $responseObj['body']['providerAccount'];
				
				$refresh = array();
				
				$refresh['statusCode'] = $res['refreshInfo']['statusCode'];
				$refresh['status'] = $res['refreshInfo']['status'];
				$refresh['statusMessage'] = $res['refreshInfo']['statusMessage'];
				
				$refresh['additionalStatus'] = '';
				if (array_key_exists('additionalStatus', $res['refreshInfo'])) {
					$refresh['additionalStatus'] = $res['refreshInfo']['additionalStatus'];	
				}
				
				$refresh['actionRequired'] = '';
				if (array_key_exists('actionRequired', $res['refreshInfo'])) {
					$refresh['actionRequired'] = $res['refreshInfo']['actionRequired'];
				}

				$refresh['message'] = '';
				if (array_key_exists('message', $res['refreshInfo'])) {
					$refresh['message'] = $res['refreshInfo']['message'];
				}
				
				// Is this ever returned for this call?
				$refresh['additionalInfo'] = '';
				if (array_key_exists('additionalInfo', $res['refreshInfo'])) {
					$refresh['additionalInfo'] = $res['refreshInfo']['additionalInfo'];	
				}

				$refresh['loginForm'] = '';
				if (array_key_exists('loginForm', $res)) {
					$refresh['loginForm'] = $res['loginForm'];
				}
							
				return $refresh;   	

			} else {

				$err = array(
					'datetime' => Carbon::now()->toDateTimeString(),
					'ip' => \Request::ip(),
					'userId' => Auth::user()->id, 
					'yslUserId' => Auth::user()->yslUserId,
					'file' => __FILE__, 
					'method' => __FUNCTION__, 
					'event' => 'Updating ProviderAccount', 
					'params' => json_encode($params), 
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

	/*	
    * Refresh providerAccount. 
    * YSL URL (PUT CURL): https://usyirestmaster.api.yodlee.com/ysl/uscnew/v1/providerAccount/?providerAccountId=1234566
    * Arguments: Array with providerAccountId and empty Login Form
    * Returns (on success): Array with providerAccountId (same as input), providerId, Account update status, and error codes or messages if any
    */
    function refreshProviderAccounts($params)
    {

    	if ( $this->yodleeUser->isActive() ) { // Checking if user is active
		
			$requestUrl = config('services.yodlee.providerAccounts.url');

			$queryArgs = array();
			$queryArgs['providerAccountIds'] = $params['providerAccountId'];
		
			if(count($queryArgs) > 0) {
            	$requestUrl = $requestUrl.'?'.http_build_query($queryArgs, '', '&');
			}

			$params = $params['params'];

			$responseObj = Utils::httpPut($requestUrl, $params, Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken);

			if ( $responseObj['httpStatus'] == '200' ) {

				// NOTE: This is slightly different in that since multiple providerAccountIds 
				// can be "refreshed", an array of providerAccountId arrays is returned.
				$res = $responseObj['body']['providerAccount'][0];
				
				$refresh = array();
				
				$refresh['statusCode'] = '';
				if (array_key_exists('statusCode', $res['refreshInfo'])) {
					$refresh['statusCode'] = $res['refreshInfo']['statusCode'];
				}
				
				$refresh['status'] = '';
				if (array_key_exists('status', $res['refreshInfo'])) {
					$refresh['status'] = $res['refreshInfo']['status'];
				}

				$refresh['statusMessage'] = '';
				if (array_key_exists('statusMessage', $res['refreshInfo'])) {
					$refresh['statusMessage'] = $res['refreshInfo']['statusMessage'];
				}
				
				$refresh['additionalStatus'] = '';
				if (array_key_exists('additionalStatus', $res['refreshInfo'])) {
					$refresh['additionalStatus'] = $res['refreshInfo']['additionalStatus'];	
				}
				
				$refresh['actionRequired'] = '';
				if (array_key_exists('actionRequired', $res['refreshInfo'])) {
					$refresh['actionRequired'] = $res['refreshInfo']['actionRequired'];
				}

				$refresh['message'] = '';
				if (array_key_exists('message', $res['refreshInfo'])) {
					$refresh['message'] = $res['refreshInfo']['message'];
				}
				
				// Is this ever returned for this call?
				$refresh['additionalInfo'] = '';
				if (array_key_exists('additionalInfo', $res['refreshInfo'])) {
					$refresh['additionalInfo'] = $res['refreshInfo']['additionalInfo'];	
				}

				$refresh['loginForm'] = '';
				if (array_key_exists('loginForm', $res)) {
					$refresh['loginForm'] = $res['loginForm'];
				}
							
				return $refresh;

			} else {

				$err = array(
					'datetime' => Carbon::now()->toDateTimeString(),
					'ip' => \Request::ip(),
					'userId' => Auth::user()->id, 
					'yslUserId' => Auth::user()->yslUserId,
					'file' => __FILE__, 
					'method' => __FUNCTION__, 
					'event' => 'Updating ProviderAccount', 
					'params' => json_encode($params), 
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
	 * Get all the provider accounts added by the user. 
	 * This includes the failed and successfully added provider accounts. 
	 * GET /{cobrandName}/v1/providers/providerAccounts
	 * Displayed on /account/status page
	 */
	public function getProviderAccounts() 
	{

		if ( $this->yodleeUser->isActive() ) { // Checking if user is active

			$request = config('services.yodlee.providerAccounts.url');
			
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

			return false;

		}
	}

	/**
	 * Get the status of add provider account action
	 * GET /{cobrandName}/v1/providers/providerAccounts/{providerAccountId}
	 */
	public function getProviderAccountDetails($providerAccountId) 
	{

		if ( $this->yodleeUser->isActive() ) { // Checking if user is active

			$request = config('services.yodlee.providerAccounts.url'). '/'.$providerAccountId;
			
			$responseObj = Utils::httpGet($request, Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken);
			
			if ( $responseObj['httpStatus'] == '200' ) {

				$res = $responseObj['body']['providerAccount'];
				
				$refresh = array();
				
				$refresh['statusCode'] = $res['refreshInfo']['statusCode'];
				$refresh['status'] = $res['refreshInfo']['status'];
				$refresh['statusMessage'] = $res['refreshInfo']['statusMessage'];
				
				$refresh['additionalStatus'] = '';
				if (array_key_exists('additionalStatus', $res['refreshInfo'])) {
					$refresh['additionalStatus'] = $res['refreshInfo']['additionalStatus'];	
				}
				
				$refresh['actionRequired'] = '';
				if (array_key_exists('actionRequired', $res['refreshInfo'])) {
					$refresh['actionRequired'] = $res['refreshInfo']['actionRequired'];
				}

				$refresh['message'] = '';
				if (array_key_exists('message', $res['refreshInfo'])) {
					$refresh['message'] = $res['refreshInfo']['message'];
				}
				
				// Is this ever returned for this call?
				$refresh['additionalInfo'] = '';
				if (array_key_exists('additionalInfo', $res['refreshInfo'])) {
					$refresh['additionalInfo'] = $res['refreshInfo']['additionalInfo'];	
				}

				$refresh['loginForm'] = '';
				if (array_key_exists('loginForm', $res)) {
					$refresh['loginForm'] = $res['loginForm'];
				}
							
				return $refresh;   		
				
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
			return false;
		}
	}

    
	public function deleteProviderAccounts($providerAccountId) 
	{

		if ( $this->yodleeUser->isActive() ) { // Checking if user is active
			
			$request = config('services.yodlee.providerAccounts.url'). '/'.$providerAccountId;

			$params = null;
			
			$responseObj = Utils::httpDelete($request, $params, Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken);
				
			if ( $responseObj['httpStatus'] == '204' ) {

				return true;

			} else {
			
				$err = array(
					'datetime' => Carbon::now()->toDateTimeString(),
					'ip' => \Request::ip(),
					'userId' => Auth::user()->id, 
					'yslUserId' => Auth::user()->yslUserId,
					'file' => __FILE__, 
					'method' => __FUNCTION__, 
					'event' => 'Deleting ProviderAccount', 
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

	public function parseAndPopulateProviderDetails($loginForm, $login, $password) {

		
		$rows = $loginForm['row'];

		$rows[0]['field'][0]['value']= $login;
		$rows[1]['field'][0]['value']= $password;

		$loginForm['row'][0]=$rows[0];
		$loginForm['row'][1]=$rows[1];

		return $loginForm;

	}


	public function parseAndPopulateLoginFormForToken($loginForm, $token) {
        
		$rows = $loginForm['row'];

        $rows[0]['field'][0]['value']= $token;
        $loginForm['row'][0]=$rows[0];
        
        return $loginForm;
    }

}
