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

    	if ( $this->yodleeUser->isActive() ) { // Checking if user is active
		
			$requestUrl = config('services.yodlee.providerAccounts.url');

			$queryArgs = array();
			$queryArgs['providerId'] = $params['provider'][0]['id'];
		
			if(count($queryArgs) > 0) {
            	$requestUrl = $requestUrl.'?'.http_build_query($queryArgs, '', '&');
			}

			$params = $params['provider'][0]['loginForm'];
			$params = array('loginForm'=>$params);
			
			$responseObj = Utils::httpPost($requestUrl, $params, Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken);
			
			// $params = json_encode($params, JSON_UNESCAPED_UNICODE);
	        //$responseObj = Utils::httpPostCurl($requestUrl ,$params, Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken);

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

    
	public function deleteProviderAccounts($providerId) 
	{

		if ( $this->yodleeUser->isActive() ) { // Checking if user is active
			
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