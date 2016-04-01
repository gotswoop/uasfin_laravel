<?php 

namespace App\Library\Yodlee;

use Auth;

class Provider {

	public function getProviderDetails($providerId) 
	{

		// SWOOP - Clean input ($providerId)
		$request = config('services.yodlee.providers.url'). '/' .$providerId;

		$responseObj = Utils::httpGet($request, Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken);

		if ( $responseObj['httpStatus'] == '200' ) {
			return $responseObj['body'];
		} else {
			dd($responseObj['error']);
		}
    }

    public function searchProviders($searchString) 
	{

		// SWOOP - Clean input ($providerId)
		$request = config('services.yodlee.providers.url'). '/?name=' .$searchString;
	
	   	$responseObj = Utils::httpGet($request, Auth::user()->yslCobrandSessionToken, null);
	
		if ( $responseObj['httpStatus'] == '200' ) {
			return $responseObj['body'];
		} else {
			dd($responseObj['error']);
		}
	}

	public function refreshProvider($providerId) 
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


    function addAccount($url, $cobSession, $userSession, $mod_provider_obj)
    {

    	$providerId = $mod_provider_obj['provider'][0]['id'];
		$providerParam = json_encode($mod_provider_obj,JSON_UNESCAPED_UNICODE);
		Utils::logMessage($fq_name,"providerParam:::>>::".$providerParam.PHP_EOL.PHP_EOL);
		$provd = new Provider();
		$response = $provd -> addAccountForProvider($url, $cobSession, $userSession, $providerId, $providerParam);
		Utils::logMessage($fq_name,">>>>>response['body']:::>>::".$response['body'].PHP_EOL.PHP_EOL);
		$responseArr = Utils::parseJson($response['body']);
		print_r($responseArr);
		return $responseArr['providerAccountId'];
		// write the code to parse and get accountId, same will be used for refreshStatus...

	}

}