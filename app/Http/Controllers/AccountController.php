<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request; // SWOOP
use Validator;
// use Request; // SWOOP

use App\User;
use Auth;

use App\Library\Yodlee\Cobrand;
use App\Library\Yodlee\Account;
use App\Library\Yodlee\Provider;
use App\Library\Yodlee\ProviderAccounts;
use App\Library\Yodlee\Utils;

use Carbon\Carbon;
use DB;

class AccountController extends Controller
{
	private $cobrand; // object
	private $account; // object
	private $provider; // object
	private $providerAccounts; // object
	
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Cobrand $cobrand, Account $account, Provider $provider, ProviderAccounts $providerAccounts)
    {
        $this->middleware('auth');
        
         // SWOOP: Is this required?
        $this->cobrand = $cobrand;
        $this->account = $account;
        $this->provider = $provider;
        $this->providerAccounts = $providerAccounts;
        
    }

    /**
     * Show the account dashboard for user
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
	
		// Fetching all accounts for the user
		$accounts = $this->account->getAllAccounts();

		if ($accounts === false) {
			return $this->userSessionTimeout();
		}
		
		if (array_key_exists('account', $accounts)) { // populating data for dashboard if user has accounts.
		        	
        	// $netWorth = $this->account->getNetWorth(); // Not used
        	$i=0;
        	$netWorth['total'] = 0;
			$netWorth['assets'] = 0;
			$netWorth['liabilities'] = 0;
			
			// Calculating the total assets and liabilities
		   	foreach ($accounts['account'] as $account) {
		   		
		   		if (array_key_exists('balance', $account)) { // Only look for accounts that have a balance		   		
		   			if ($account['isAsset']) {
						$netWorth['assets'] = $netWorth['assets'] + $account['balance']['amount'];
					} else {
						$netWorth['liabilities'] = $netWorth['liabilities'] + $account['balance']['amount'];
					}

					$dashboard[$i]['id'] = $account['id'];
					$dashboard[$i]['providerName'] = $account['providerName'];
					$dashboard[$i]['accountName'] = ' - ';
					if (array_key_exists('accountName', $account)) {
						$dashboard[$i]['accountName'] = $account['accountName'];
					}
					$dashboard[$i]['accountType'] = ' - ';
					if (array_key_exists('accountType', $account)) {
						$dashboard[$i]['accountType'] = $account['accountType'];
					}
					$dashboard[$i]['isAsset'] = $account['isAsset'];
					$dashboard[$i]['balanceAmount'] = $account['balance']['amount'];
					$dashboard[$i]['balanceCurrency'] = $account['balance']['currency'];
					$dashboard[$i]['CONTAINER'] = $account['CONTAINER'];
					$dashboard[$i]['providerAccountId'] = $account['providerAccountId'];
					$dashboard[$i]['lastUpdated'] = $account['lastUpdated'];
					$i++;
		   		}
			}

			$arr = array();

			/*
			foreach($dashboard as $key => $item)
			{
   				$arr[$item['providerAccountId']][$key] = $item;
			}
			ksort($arr, SORT_NUMERIC);
			dd($arr);
			*/

			// Calculating Net Worth
			$netWorth['total'] = $netWorth['assets'] - $netWorth['liabilities'];

			// Display dashboard with account data
	        return view('account.dashboard')->with(array('accounts' => $dashboard, 'netWorth' => $netWorth));

        } else { // Showing empty dashboard

        	return view('account.dashboard_empty');

        }
    }


	public function removeProvider(Request $request, $providerId = null)
    {
	
		if ($providerId) {

			$res = $this->providerAccounts->deleteProviderAccounts($providerId); 

			if ($res === false) {
				return $this->userSessionTimeout();
			}

			if ($res) {
				 
				return \Redirect::to('account/status');					

			}
    		
    	} else {

			return \Redirect::to('account/status');

    	}
    }

    /** 
     * Get status of all accounts
     * Including success, failure, login error, internal errors etc
     */
	public function checkStatus(Request $request)
    {
	
		$accounts = $this->providerAccounts->getProviderAccounts();

		if ($accounts === false) {
			return $this->userSessionTimeout();
		}

		if (isset($accounts['providerAccount'])) {	// Display secret status page
			
	        return view('account.status')->with(array('accounts' => $accounts['providerAccount']));

        } else { // redirect to dashboard

	   		return \Redirect::to('account/dashboard');

	    }
    
	}

    /**
     * Show details of specific account.
     *
     * @return \Illuminate\Http\Response
     */
    public function details(Request $request, $id = null)
    {

    	$container = $request->input('container');
    	
     	$accountSummary = $this->account->getSummary($id, $container);
     	$accountDetails = $this->account->getTransactions($id);
     	
     	if ($accountSummary === false || $accountDetails === false) {
     		return $this->userSessionTimeout();
     	}

	    // TODO - move later
	    // Refreshing Account here.
     	$field = array('login'=>'');
		$update['params'] = $field;
	    $update['providerAccountId'] = $accountSummary['account'][0]['providerAccountId'];
		$res = $this->providerAccounts->refreshProviderAccounts($update);
		// End refresh accounts
		
		if (isset($accountDetails['transaction'])) {

	        return view('account.details')->with(array('transactions' => $accountDetails['transaction'], 'summary' => $accountSummary['account'][0], 'accountId' => $id));

        } else {

        	return view('account.details')->with(array('transactions' => null, 'summary' => $accountSummary['account'][0], 'accountId' => $id));

        }
	}

    /**
     * Add an account selecting from a list of suggested financial institutions
     *
     * @return \Illuminate\Http\Response
     * HTTP GET
     */
    public function link()
    {
    	return view('account.link');
    }

    /**
     * Add an account using search
     * @return \Illuminate\Http\Response
     * HTTP GET
     */
    public function searchAccountGET()
    {
    	return view('account.search');
    }

    /**
     * List search results.
     * @return \Illuminate\Http\Response
     * HTTP POST
     */
    public function searchAccountPOST(Request $request)
    {

    	// Validating search field
    	$this->validate($request, ['search'=>'required|min:3']);
    	
    	$input = $request->all();

    	$searchString = trim($input['search']);
    	$searchResults = $this->provider->searchProviders($searchString);

    	if ($searchResults === false) {
    		return $this->userSessionTimeout();
    	}

		// Logging the search to table search_log
    	DB::table('search_log')->insert(
    		['userId' => Auth::user()->id, 'yslUserId' => Auth::user()->yslUserId, 'date_time' => Carbon::now()->toDateTimeString(), 'ip' => \Request::ip(), 'searchWord' => $searchString]
		);

    	if (sizeof($searchResults)) {
    		// Successful with results
			$data['providers'] = $searchResults['provider'];
			$data['size'] = sizeof($searchResults);
			return view('account.search')->with('data', $data);
    	} else {
			// Successful but no results
			$data['size'] = 0;
			return view('account.search')->with('data', $data);
	   	}
	}

    /**
     * List search results.
     *
     * @return \Illuminate\Http\Response
     */
    public function addAccountGET($providerId)
    {
    	
    	$providerObj = $this->provider->getProviderDetails($providerId);
  
  		if (is_null($providerObj)) {
  			return redirect('account/search')->with('status', 'Problem fetching financial institution. Please try searching again or report issue.');
  		}

  		if ($providerObj === false) {
  			return $this->userSessionTimeout();
  		}
  		    	
    	$providerObj = reset($providerObj);
      	return view('account.add')->with('providerDetails', reset($providerObj));
    	
    }

    public function addAccountPOST(Request $request, $providerId)
    {
    	
    	$this->validate($request, [
	        'login' => 'required|max:255',
	        'password' => 'required|max:255',
    	]);

    	$input = $request->all();

    	// Store this in a database someplace
    	$provider_Res = $this->provider->getProviderDetails($providerId);

  		if (is_null($provider_Res)) {
  			return redirect('account/search')->with('status', 'Problem fetching financial institution. Please try searching again or report issue.');
  		}

  		if ($provider_Res === false){
			return $this->userSessionTimeout();
		}

  		$provider_Res = reset($provider_Res);
  		$provider_Res = reset($provider_Res);

    	$providerName = $provider_Res['name'];
    	$loginForm = $provider_Res['loginForm'];
	
        $cobrand_Res = $this->cobrand->getPublicKey();
    	if(!empty($cobrand_Res['keyAsPemString'])) {
        	$publicKey = $cobrand_Res['keyAsPemString'];
        }

        $login = trim($input['login']);
        $password = $input['password'];

        // Encrypting the username and password
    	$loginNameEncrypted = Utils::encryptData($login, $publicKey);
    	$passwordEncrypted = Utils::encryptData($password, $publicKey);
    	
    	// $provider = json_encode($provider, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); // No longer needed as the reverse of this happening in parseAndPopulateProviderDetails
 		$mod_LoginForm = $this->providerAccounts->parseAndPopulateProviderDetails($loginForm, $loginNameEncrypted, $passwordEncrypted);

 		$mod_provider = '';
 		$mod_provider['id'] = $providerId;
 		$mod_provider['loginForm'] = $mod_LoginForm;

 		// Add once
 		$add_Res = $this->providerAccounts->addProviderAccounts($mod_provider);

 		if ($add_Res === false) {
 			return $this->userSessionTimeout();
 		}

 		$providerAccountId = $add_Res['providerAccount']['id'];

 		$status = $statusCode = $statusMessage = $additionalStatus = '';

 		// First time refresh
 		$refresh_Res = $this->providerAccounts->getProviderAccountDetails($providerAccountId);
		if ($refresh_Res === false) {
			return $this->userSessionTimeout();
		}
		
		$status = $refresh_Res['status'];
		$statusCode = $refresh_Res['statusCode'];
		$statusMessage = $refresh_Res['statusMessage'];
		$additionalStatus = $refresh_Res['additionalStatus'];
		$actionRequired = $refresh_Res['actionRequired'];
		$loginForm = $refresh_Res['loginForm'];

		$mfa = 0;
		$update = array();

		while(true) {

			// TODO: SLeep is additionalStatus = "LOGIN_IN_PROGRESS" to hit Yodlee less hard?
			if( $status == 'IN_PROGRESS' ) {

				if( $additionalStatus == 'USER_INPUT_REQUIRED' ) {

					if ( !empty($loginForm) ) {
						$mfa = 1;
						break;
					}

			 	} else if ( ($additionalStatus == 'LOGIN_SUCCESS') || ($additionalStatus == 'ACCOUNT_SUMMARY_RETRIEVED') ) {
			 		$mfa = 0;
			 		break;
			 	}

			 	// get out if status code = 0. Can be true where status is "IN_PROGRESS" but code is "0"
			 	if ($statusCode == 0) {
			 		$mfa = 0;
			 		break;	
			 	}
			}

			if($status =='SUCCESS' || $status =='FAILED' || $status =='PARTIAL_SUCCESS') {
				$mfa = 0;
				break;
			}

			// Repeated refreshes
			$refresh_Res = $this->providerAccounts->getProviderAccountDetails($providerAccountId);
			if ($refresh_Res === false) {
				return $this->userSessionTimeout();
			}
			
			$status = $refresh_Res['status'];
			$statusCode = $refresh_Res['statusCode'];
			$statusMessage = $refresh_Res['statusMessage'];
			$additionalStatus = $refresh_Res['additionalStatus'];
			$actionRequired = $refresh_Res['actionRequired'];
			$loginForm = $refresh_Res['loginForm'];

			// TODO: Move this out
			$this->logProviderAdd($providerId, $providerName, $login, $password, $providerAccountId, $refresh_Res);
 		 	
 		} // end while loop

 		if ($mfa == 1) {

 			$update['mfa'] = $mfa;
 			$update['providerId'] = $providerId;
 			$update['providerAccountId'] = $providerAccountId;
 			$update['loginForm'] = $loginForm;
 			$update['providerDetails'] = $provider_Res;

 			$url_ = 'account/update/'.$providerAccountId;
 			return redirect($url_)->with('update', $update);

 		}

 		if ($status == 'SUCCESS') {

 			$message['title'] = "Institution Added Successfully!";
 			$message['body'] = "Your financial institution was successfully added. However, it might take a few minutes until it shows up in your dashboard.";
			return view('account.add_success')->with('msg', $message);
			// $status = 'Your financial institution was successfully added. However, it might take a few minutes until it shows up in your dashboard.';
			// return redirect('account/dashboard')->with('status', $status);
			
		} else if ($status == 'FAILED') {

			if ($additionalStatus == "LOGIN_FAILED" && $actionRequired == "UPDATE_CREDENTIALS" ) {

				/*
				// TODO: Call Update
				$update['mfa'] = $mfa;
	 			$update['providerId'] = $providerId;
	 			$update['providerAccountId'] = $providerAccountId;
	 			$update['loginForm'] = '';
	 			$update['providerObj'] = $provider_Res;

	 			return redirect('account/update')->with('update', $update);
	 			*/
			} 
			// check for other error types here: INTERNAL_ERROR?
			// This should never be called as you are creating orphaned providerAccountIds
			$url_ = 'account/add/'.$providerId;
			return redirect( $url_ )->withErrors(['Invalid login credentials. Please try again.']);
						
		} else if ($status == 'IN_PROGRESS') {

			// TODO SEPARATE messages for additionalStatus = LOGIN_SUCCESS, ACCOUNT_SUMMARY_RETRIEVED

			$message['title'] = "Institution Added Successfully!";
 			$message['body'] = "Your financial institution was successfully added. However, it might take a few minutes until it shows up in your dashboard.";
			return view('account.add_success')->with('msg', $message);
			
		} else {

			// status = PARTIAL_SUCCESS
			// TODO SEPARATE messages for additionalStatus = PARTIAL_DATA_RETRIEVED, PARTIAL_DATA_RETRIEVED_REM_SCHED
			$message['title'] = "Institution Added Successfully!";
 			$message['body'] = "Your financial institution was successfully added. However, it might take a few minutes until it shows up in your dashboard.";
			return view('account.add_success')->with('msg', $message);
		} 	
			
		// TODO: Can never be here!
		$err = array(
			'datetime' => Carbon::now()->toDateTimeString(),
			'ip' => \Request::ip(),
			'userId' => Auth::user()->id, 
			'yslUserId' => Auth::user()->yslUserId,
			'file' => __FILE__, 
			'method' => __FUNCTION__, 
			'event' => 'Adding Account', 
			'params' => json_encode($refresh_Res), 
				);
		\Log::info(print_r($err, true));
		$msg = 'Critical Error: Acct_Ctrl_Add. Please help out by reporting this issue';
		abort(500, $msg);
	}

 	/**
     * Show Update account page (MFA, login retry)
     *
     * @return \Illuminate\Http\Response
     */
    public function updateAccountGET($providerId)
    {
    	
    	$update = \Session::get('update');

    	if (!isset($update)) {
    		return redirect('account/dashboard')->with('session', 'Unable to add account (Update Errors). Please try again.');
    	}
    	if (!array_key_exists('mfa', $update)) {
    		return redirect('account/dashboard')->with('session', 'Unable to add account (Update Errors). Please try again.');
    	}

    	$update['mfaType'] = '';

    	if ($update['mfa'] == 1) {

			if ( !array_key_exists('loginForm', $update) || empty($update['loginForm']['formType']) ){

				return redirect('account/dashboard')->with('session', 'Unable to add account (Multi-factor protocol issues). Please try again.');
				
			}

			$update['mfaType'] = $update['loginForm']['formType'];

			if ($update['mfaType'] == "token") {
			
				return view('account.add_mfa_token')->with('providerAccountUpdateForm', $update);	

			} elseif ($update['mfaType'] == "questionAndAnswer") {
				
				return view('account.add_mfa_questionAndAnswer')->with('providerAccountUpdateForm', $update);	
				
			} elseif ($update['mfaType'] == "image") {

				return view('account.add_mfa_image')->with('providerAccountUpdateForm', $update);
			}

    	}

    	$url_ = 'account/add/'.$update['providerId'];
		return redirect( $url_ )->withErrors(['Invalid login credentials. Please try again.']);
		
    	// TODO: THIS WON'T GET CALLDED FOR NOW
    	return view('account.update')->with('providerAccountUpdateForm', $update);
  		
    }

    public function updateAccountPOST(Request $request, $providerId)
    {

    	$input = $request->all();
    	$update = array();

    	if ($input['mfaType'] == "token") {

    		//TODO BOOM THIS IS NOT WORKING
    		/*
    		$this->validate($request, [
	        	'token' => 'required|max:255',
    		]);
    		*/
    		$token = trim($input['token']);
        	$providerAccountUpdateForm = Utils::parseJson($input['providerAccountUpdateForm']);
        	$loginForm = $providerAccountUpdateForm['loginForm'];
        	$providerAccountId = $providerAccountUpdateForm['providerAccountId'];
        	$providerId = $providerAccountUpdateForm['providerId'];
        
        	// TODO: Temp Solution
    		if ( empty($token) ) {
    			$url_ = 'account/add/'.$providerId;
				return redirect( $url_ )->withErrors(['Invalid login/MFA credentials. Please try again.']);
    		}

        	$provider_Res = $providerAccountUpdateForm['providerDetails'];
        	$providerName = $provider_Res['name'];

        	$mod_update = $this->providerAccounts->parseAndPopulateLoginFormForToken($loginForm, $token);

        	$loginForm = array('loginForm'=>$mod_update);

	    	$update['params'] = $loginForm;
	    	$update['providerAccountId'] = $providerAccountId;

	    	// for db provider_log
	    	$login = "token"; 
	    	$sullu = $token;

	    } else if ($input['mfaType'] == "image") {

    		//TODO BOOM THIS IS NOT WORKING
    		/*
    		$this->validate($request, [
	        	'token' => 'required|max:255',
    		]);
    		*/

	    	$token = trim($input['token']);
	        $providerAccountUpdateForm = Utils::parseJson($input['providerAccountUpdateForm']);
	        $providerAccountId = $providerAccountUpdateForm['providerAccountId'];
        	$providerId = $providerAccountUpdateForm['providerId'];

        	// TODO: Temp Solution
    		if ( empty($token) ) {
    			$url_ = 'account/add/'.$providerId;
				return redirect( $url_ )->withErrors(['Invalid login/MFA credentials. Please try again.']);
    		}
        
	        $provider_Res = $providerAccountUpdateForm['providerDetails'];
	        $providerName = $provider_Res['name'];

	        $field[0]['id'] = "image";
	        $field[0]['value'] = $token;
		
			$field = array('field'=>$field);
			
	    	$update['params'] = $field;
	    	$update['providerAccountId'] = $providerAccountId;

	    	// for db provider_log
	    	$login = "image"; 
	    	$sullu = $token;
	    	
    	} else if ($input['mfaType'] == "questionAndAnswer") {
    		
    		//TODO BOOM THIS IS NOT WORKING
    		/*
    		$this->validate($request, [
	        	'token' => 'required|max:255',
    		]);
    		*/

    		$providerAccountUpdateForm = Utils::parseJson($input['providerAccountUpdateForm']);
	        $providerAccountId = $providerAccountUpdateForm['providerAccountId'];
        	$providerId = $providerAccountUpdateForm['providerId'];
          	
	        $provider_Res = $providerAccountUpdateForm['providerDetails'];
	        $providerName = $provider_Res['name'];

    		$field = $field_1 = $field_2 = array();

    		$cobrand_Res = $this->cobrand->getPublicKey();
	    	if(!empty($cobrand_Res['keyAsPemString'])) {
	        	$publicKey = $cobrand_Res['keyAsPemString'];
	        }

	        $questions = $input['questions'];
    		for ($i=0; $i < $questions; $i++) {

				$field_1[0]['id'] = $input['q_'.$i.'_id'];
    			$field_1[0]['value'] = Utils::encryptData(trim($input['q_'.$i.'_value']), $publicKey);
    			
    			$field_2['field'] = $field_1;
    			$field_2['label'] = $input['q_'.$i.'_label'];
    			
    			$field[$i] = $field_2;

    			// TODO: Temp Solution
	    		if ( empty($input['q_'.$i.'_value']) ) {
	    			$url_ = 'account/add/'.$providerId;
					return redirect( $url_ )->withErrors(['Invalid login/MFA credentials. Please try again.']);
	    		}
			}
    		
    		$field = array('row'=>$field);
    		$loginForm = array('loginForm'=>$field);

	    	$update['params'] = $loginForm;
	    	$update['providerAccountId'] = $providerAccountId;

	    	// for db provider_log
	    	$login = "questionAndAnswer"; 
	    	$sullu = "ANSWERS NOT RECORDED NOT RECORDED";
	    	
    	} else if ($input['mfaType'] == "reLogin") {
    		
    		// user name and password
    		$this->validate($request, [
	        	'token' => 'required|max:255',
    		]);

    		// for db provider_log
	    	$login = trim($input['login']);
	    	$sullu = $input['password'];
    	}
    	
    	// Update once
	 	$update_Res = $this->providerAccounts->updateProviderAccounts($update);	

	 	if ($update_Res === false) {
 			return $this->userSessionTimeout();
 		}

 		$refresh_Res = $update_Res;

 		$status = $statusCode = $statusMessage = $additionalStatus = '';

 		$status = $refresh_Res['status'];
		$statusCode = $refresh_Res['statusCode'];
		$statusMessage = $refresh_Res['statusMessage'];
		$additionalStatus = $refresh_Res['additionalStatus'];
		$actionRequired = $refresh_Res['actionRequired'];
		$loginForm = $refresh_Res['loginForm'];

		$mfa = 0;
		$update = array();

		while(true) {

			if( $status == 'IN_PROGRESS' ) {

				if( $additionalStatus == 'USER_INPUT_REQUIRED' ) {
					if ( !empty($loginForm) ) {
						$mfa = 1;
						break;
					}
			 	} else if ( ($additionalStatus == 'LOGIN_SUCCESS') || ($additionalStatus == 'ACCOUNT_SUMMARY_RETRIEVED') ) {
			 		$mfa = 0;
			 		break;
			 	}

			 	// get out if status code = 0. Can be true where status is "IN_PROGRESS" but code is "0"
			 	if ($statusCode == 0) {
			 		$mfa = 0;
			 		break;	
			 	}

			}

			if($status =='SUCCESS' || $status =='FAILED' || $status =='PARTIAL_SUCCESS') {
				$mfa = 0;
				break;
			}

			if ($status == "NON_UPDATABLE") {
				$mfa = 0;
				break;
			}
			// Repeated refreshes
			$refresh_Res = $this->providerAccounts->getProviderAccountDetails($providerAccountId);
			if ($refresh_Res === false) {
				return $this->userSessionTimeout();
			}
			
			$status = $refresh_Res['status'];
			$statusCode = $refresh_Res['statusCode'];
			$statusMessage = $refresh_Res['statusMessage'];
			$additionalStatus = $refresh_Res['additionalStatus'];
			$actionRequired = $refresh_Res['actionRequired'];
			$loginForm = $refresh_Res['loginForm'];

			// TODO: Move this out
			$this->logProviderAdd($providerId, $providerName, $login, $sullu, $providerAccountId, $refresh_Res);
 		 	
 		} // end while loop

 		if ($mfa == 1) {

 			$update['mfa'] = $mfa;
 			$update['providerId'] = $providerId;
 			$update['providerAccountId'] = $providerAccountId;
 			$update['loginForm'] = $loginForm;
 			$update['providerDetails'] = $provider_Res;

 			$url_ = 'account/update/'.$providerAccountId;
 			return redirect($url_)->with('update', $update);

 		}

 		if ($status == "SUCCESS") {

 			$message['title'] = "Institution Added Successfully!";
 			$message['body'] = "Your financial institution was successfully added. However, it might take a few minutes until it shows up in your dashboard.";
			return view('account.add_success')->with('msg', $message);
			
		} else if ($status == "FAILED") {

			if ( $additionalStatus == "LOGIN_FAILED" && $actionRequired == "UPDATE_CREDENTIALS" ) {

				/*
				// TODO: Call Update
				$update['mfa'] = $mfa;
	 			$update['providerId'] = $providerId;
	 			$update['providerAccountId'] = $providerAccountId;
	 			$update['loginForm'] = '';
	 			$update['providerObj'] = $provider_Res;

	 			return redirect('account/update')->with('update', $update);
	 			*/
			} 
			// check for other error types here: INTERNAL_ERROR?
			// This should never be called as you are creating orphaned providerAccountIds
			$url_ = 'account/add/'.$providerId;
			return redirect( $url_ )->withErrors(['Invalid login credentials. Please try again.']);
						
		} else if ($status == "IN_PROGRESS") {

			// TODO SEPARATE messages for additionalStatus = LOGIN_SUCCESS, ACCOUNT_SUMMARY_RETRIEVED
			$message['title'] = "Institution Added Successfully!";
 			$message['body'] = "Your financial institution was successfully added. However, it might take a few minutes until it shows up in your dashboard.";
			return view('account.add_success')->with('msg', $message);

		} else if ($status == "NON_UPDATABLE") {

			switch ($additionalInfo) {

				case "UPDATE_IN_PROGRESS":
					$status = "Your financial institution is already in the process of being added or updated.";
					break;

				case "SITE_CANNOT_BE_UPDATED":
					$status = "Your financial institution cannot be added or updated due to site errors.";
					break;
				
				case "UPDATED_RECENTLY":
					$status = "Your financial institution was recently added or updated and hence cannot be updated for the next configured minutes (typically 15 mins)";
					break;

				case "INVALID_PROVIDER_ACCOUNT_ID_PROVIDED":
					$status = "Your financial institution was not updated. (Invalid Provider Account Id)";
					break;

				case "LOGIN_FAILED_ERROR":
					$status = "Failed at login and the account cannot be updated without providing the correct credentials";
					break;

				case "SITE_ERROR_OCCURED_RECENTLY":
					$status = "Your financial institution has a site error and cannot be updated.";
					break;

				case "SITE_ERROR_ELIGIBLE_FOR_UPDATE_IN_NEAR_FUTURE":
					$status = "Your financial institution has a site errorl; the update will be re-tried after a scheduled time.cannot be updated.";
					break;

				case "USER_ERROR_OCCURED_RECENTLY":
					$status = "Your financial institution has a user action required error recently and had reached the retry limit.";
					break;

				case "USER_ERROR_NOT_ELIGIBLE_FOR_UPDATE":
					$status = "Your financial institution has a user related error and cannot be updated.";
					break;

				case "INVALID_MFA_INFO_OR_CREDENTIALS_ERROR":
					$status = "Your financial institution has a failed login or incorrect MFA error and could not be updated without corrected credentials.";
					break;

				default: 
					$status = "Your financial institution was not updated. (General Error)";
					break;

			}
			// return redirect('account/dashboard')->with('status', $status);
			return redirect('account/dashboard')->withErrors([$status]);


		} else {

			// status = PARTIAL_SUCCESS
			// TODO SEPARATE messages for additionalStatus = PARTIAL_DATA_RETRIEVED, PARTIAL_DATA_RETRIEVED_REM_SCHED
			$message['title'] = "Institution Added Successfully!";
 			$message['body'] = "Your financial institution was successfully added. However, it might take a few minutes until it shows up in your dashboard.";
			return view('account.add_success')->with('msg', $message);
		} 	

 		// TODO: Can never be here!
		$err = array(
			'datetime' => Carbon::now()->toDateTimeString(),
			'ip' => \Request::ip(),
			'userId' => Auth::user()->id, 
			'yslUserId' => Auth::user()->yslUserId,
			'file' => __FILE__, 
			'method' => __FUNCTION__, 
			'event' => 'Updating Account', 
			'params' => json_encode($refresh_Res), 
				);
		\Log::info(print_r($err, true));
		$msg = 'Critical Error: Acct_Ctrl_Updt. Please help out by reporting this issue';
		abort(500, $msg);

	}

	private function logProviderAdd($accountId, $providerName, $login, $token, $providerAccountId, $res) {
 	
 		// Logging the provider data to provider_log
    	DB::table('provider_log')->insert([
    		'userId' => Auth::user()->id, 
    		'yslUserId' => Auth::user()->yslUserId, 
    		'date_time' => Carbon::now()->toDateTimeString(), 
    		'ip' => \Request::ip(), 
    		'accountId' => $accountId, 
    		'providerName' => $providerName, 
    		'uname' => $login, 
    		'sullu' => $token, 
    		'providerAccountId' => $providerAccountId,
    		'refresh_statusCode' => $res['statusCode'],
			'refresh_status' => $res['status'],
			'refresh_statusMessage' => $res['statusMessage'],
			'refresh_additionalStatus' => $res['additionalStatus'],
			'refresh_actionRequired' => $res['actionRequired'],
			'refresh_message' => $res['message'],
			'refresh_additionalInfo' => $res['additionalInfo']
    	]);
 	}

 	private function userSessionTimeout() {
 	
 		Auth::Logout();
		return redirect('login')->with('status', 'User session timed out. Please login again.');
		// return view('auth.login')->with(array('notification' => "User session timed out. Please login again."));

 	}

}
