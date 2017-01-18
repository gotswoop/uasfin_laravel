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

    /*	
    * 
    * YSL URL (POST CURL): https://usyirestmaster.api.yodlee.com/ysl/uscnew/v1/providerAccount/?providerId=643
    */
    function addProviderAccounts($params)
    {

    	// Checking if user is active
		if ( $this->yodleeUser->isActive( Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken ) ) {
		
			$requestUrl = config('services.yodlee.providerAccounts.url');

			$queryArgs = array();
			$queryArgs['providerId'] = $params['provider'][0]['id'];
		
			if(count($queryArgs) > 0) {
            	$requestUrl = $requestUrl.'?'.http_build_query($queryArgs, '', '&');
			}

			$params = array('loginForm'=>$params['provider'][0]['loginForm']);
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

		// Checking if user is active
		if ( $this->yodleeUser->isActive( Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken ) ) {

			$request = config('services.yodlee.providerAccounts.url'). '/'.$providerAccountId;
			
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

			return false;
		}
	}

    
	public function deleteProviderAccounts($providerId) 
	{

		// Checking if user is active
		if ( $this->yodleeUser->isActive( Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken ) ) {

			$params = array('providerAccountId' => $providerId);

			$request = config('services.yodlee.providerAccounts.url'). '/'.$providerId;
			
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

			return false;
			
		}
	}

	public function parseAndPopulateProviderDetails($provider, $field_value_0, $field_value_1) {

		// $resObj = Utils::parseJson($provider);
		// $providerObj = $resObj['provider'];
		
		$providerObj = $provider['provider'];

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

}