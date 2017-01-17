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
	 */
    public function searchProviders($searchString) 
	{

		// Checking if user is active
		if ( $this->yodleeUser->isActive( Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken ) ) {
			
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

			// Logout user when Yodlee session is inactive
			Auth::Logout();
			return false;

		}
	}

	/** 
	 * Fetch information about the provider such as name, details, login form etc.
	 * Called to populate login screen
	 */
	public function getProviderDetails($providerId) 
	{

		// SWOOP - Clean input ($providerId)

		// Checking if user is active
		if ( $this->yodleeUser->isActive( Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken ) ) {

			$request = config('services.yodlee.providers.url'). '/' .$providerId;

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
					'event' => 'Fetching provider details', 
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

	public function parseAndPopulateProviderDetails($provider,$field_value_0,$field_value_1) {

		$resObj = Utils::parseJson($provider);

		$providerObj = $resObj['provider'];

		$loginForm = $providerObj[0]['loginForm'];	

		$rows = $loginForm['row'];

		$rows[0]['field'][0]['value']= $field_value_0;

		$rows[1]['field'][0]['value']= $field_value_1;

		$loginForm['row'][0]=$rows[0];

		$loginForm['row'][1]=$rows[1];

		$providerObj[0]['loginForm'] = $loginForm;

		$mod_provider_obj = array('provider'=>$providerObj);

		return $mod_provider_obj;
	}


	// -------------------- NOT USED YET ----------------------------------

	public function parseAndPopulateLoginFormForToken($refresh) {
        
        $resObj = Utils::parseJson($refresh);
        $loginForm = $resObj['loginForm'];
        $providerParam = json_encode($loginForm,JSON_UNESCAPED_UNICODE);
        echo "<<<>>>:::".$providerParam.PHP_EOL.PHP_EOL;
        $formType = $loginForm['formType'];
        echo PHP_EOL."formType :::".$formType.PHP_EOL;
        
        if(empty($formType)) {
          echo PHP_EOL.":::Inside Else Scenario:::".PHP_EOL;
          return null;
        } else if($formType == 'token') {
          echo PHP_EOL.":::Token Scenario:::".PHP_EOL;
          $rows = $loginForm['row'];
          $rows[0]['field'][0]['value']= '123456';
          $loginForm['row'][0]=$rows[0];
        } else if($formType=='questionAndAnswer') {
          echo PHP_EOL.":::Q&A Scenario:::".PHP_EOL;
          $rows = $loginForm['row'];
          $rows[0]['field'][0]['value']= 'Texas';
          $rows[1]['field'][0]['value']= 'w3schools';
          $loginForm['row'][0]=$rows[0];
          $loginForm['row'][1]=$rows[1];
        } else if($formType=='image') {
          echo PHP_EOL.":::Image Scenario:::".PHP_EOL;
          $rows = $loginForm['row'];
          $rows[0]['field'][0]['value']= '5678';
          $loginForm['row'][0]=$rows[0];
        }
      
        $providerParam = json_encode($loginForm,JSON_UNESCAPED_UNICODE);
        echo "<<<>>>:::".$providerParam.PHP_EOL.PHP_EOL;
        $resObj['loginForm'] = $loginForm;
          $mod_loginForm_obj = array('loginForm'=>$resObj['loginForm']);
        //$mod_loginForm_obj_str = json_encode($mod_loginForm_obj,JSON_UNESCAPED_UNICODE);
        //echo "<<<>>>:::".$mod_loginForm_obj_str.PHP_EOL.PHP_EOL;
        return $mod_loginForm_obj;
    }

    public function parseAndPopulateLoginFormForQuesAns($refresh) {
        $resObj = Utils::parseJson($refresh);
        $loginForm = $resObj['loginForm'];
        $providerParam = json_encode($loginForm,JSON_UNESCAPED_UNICODE);
        echo "<<<>>>:::".$providerParam.PHP_EOL.PHP_EOL;
        $rows = $loginForm['row'];
        $rows[0]['field'][0]['value']= 'Texas';
        $rows[1]['field'][0]['value']= 'w3schools';
        $loginForm['row'][0]=$rows[0];
        $loginForm['row'][1]=$rows[1];
        $providerParam = json_encode($loginForm,JSON_UNESCAPED_UNICODE);
        echo "<<<>>>:::".$providerParam.PHP_EOL.PHP_EOL;
        $resObj['loginForm'] = $loginForm;
          $mod_loginForm_obj = array('loginForm'=>$resObj['loginForm']);
        //$mod_loginForm_obj_str = json_encode($mod_loginForm_obj,JSON_UNESCAPED_UNICODE);
        //echo "<<<>>>:::".$mod_loginForm_obj_str.PHP_EOL.PHP_EOL;
        return $mod_loginForm_obj;
    }


	function addAccount($params)
    {

    	// Checking if user is active
		if ( $this->yodleeUser->isActive( Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken ) ) {

	    	$providerId = $params['provider'][0]['id'];

	    	$requestUrl = config('services.yodlee.providers.url'). '/' .$providerId;

	    	$params = json_encode($params, JSON_UNESCAPED_UNICODE);

	    	$responseObj = Utils::httpPostCurl($requestUrl ,$params, Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken);

			if ( $responseObj['httpStatus'] == '201' ) {

				return $responseObj['body'];

			} else {

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

	public function refreshAllProviders() // NOT USED YET
	{

		$request = config('services.yodlee.refreshUrl'). '/' .$providerId;

		$responseObj = Utils::httpGet($request, Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken);

		if ( $responseObj['httpStatus'] == '200' ) {
			return $responseObj['body'];
		} else {
			dd($responseObj['error']);
		}
	}


	public function refreshProvider($providerId)  // NOT CORRECT
	{

		// SWOOP - Clean input ($providerId)
		$request = config('services.yodlee.refreshUrl'). '/' .$providerId;

		$responseObj = Utils::httpGet($request, Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken);
		
		if ( $responseObj['httpStatus'] == '200' ) {

			return $responseObj['body'];

		} else {

			dd($responseObj['error']);

		}
	}
}
