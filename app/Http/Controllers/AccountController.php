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
			$this->userSessionTimeout();
			return;
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

			// Calculating Net Worth
			$netWorth['total'] = $netWorth['assets'] - $netWorth['liabilities'];

			// Display dashboard with account data
	        return view('account.dashboard')->with(array('accounts' => $dashboard, 'netWorth' => $netWorth));

        } else {	// Showing empty dashboard

        	return view('account.dashboard_empty');

        }
    }


	public function removeProvider(Request $request, $providerId = null)
    {
	
		if ($providerId) {

			$res = $this->providerAccounts->deleteProviderAccounts($providerId); 

			if ($res === false) {
				$this->userSessionTimeout();
				return;
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
			$this->userSessionTimeout();
			return;
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
     		$this->userSessionTimeout();
     		return;
     	}
		
		if (isset($accountDetails['transaction'])) {

	        return view('account.details')->with(array('transactions' => $accountDetails['transaction'], 'summary' => $accountSummary['account'][0]));

        } else {

        	return view('account.details')->with(array('transactions' => null, 'summary' => $accountSummary['account'][0]));

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

    	$searchResults = $this->provider->searchProviders($input['search']);

    	if ($searchResults === false) {
    		$this->userSessionTimeout();
    		return;
    	}

		// Logging the search to table search_log
    	DB::table('search_log')->insert(
    		['userId' => Auth::user()->id, 'yslUserId' => Auth::user()->yslUserId, 'date_time' => Carbon::now()->toDateTimeString(), 'ip' => \Request::ip(), 'searchWord' => $input['search']]
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
  			$this->userSessionTimeout();
  			return;
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
			$this->userSessionTimeout();
			return;
  		}

    	$providerName = $provider_Res['provider'][0]['name'];
	
        $cobrand_Res = $this->cobrand->getPublicKey();
    	if(!empty($cobrand_Res['keyAsPemString'])) {
        	$publicKey = $cobrand_Res['keyAsPemString'];
        }

        // Encrypting the username and password
    	$loginNameEncrypted = Utils::encryptData($input['login'], $publicKey);
    	$passwordEncrypted = Utils::encryptData($input['password'], $publicKey);
    	
    	// $provider = json_encode($provider, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); // No longer needed as the reverse of this happening in parseAndPopulateProviderDetails
 		$mod_provider = $this->providerAccounts->parseAndPopulateProviderDetails($provider_Res, $loginNameEncrypted, $passwordEncrypted);

 		// Add once
 		$add_Res = $this->providerAccounts->addProviderAccounts($mod_provider);

 		if ($add_Res === false) {
 			$this->userSessionTimeout();
 			return;
 		}

 		$providerAccountId = $add_Res['providerAccount']['id'];

 		$status = $statusCode = $statusMessage = $additionalStatus = '';

 		// First time refresh
 		$refresh_Res = $this->providerAccounts->getProviderAccountDetails($providerAccountId);
		if ($refresh_Res === false) {
			$this->userSessionTimeout();
			return;
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

			if( $status == 'IN_PROGRESS' && $additionalStatus == 'USER_INPUT_REQUIRED' ) {
				if ( !empty($loginForm) ) {
					$mfa = 1;
					break;
				} elseif ($status != 'IN_PROGRESS') {
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
				$this->userSessionTimeout();
				return;
			}
			
			$status = $refresh_Res['status'];
			$statusCode = $refresh_Res['statusCode'];
			$statusMessage = $refresh_Res['statusMessage'];
			$additionalStatus = $refresh_Res['additionalStatus'];
			$actionRequired = $refresh_Res['actionRequired'];
			$loginForm = $refresh_Res['loginForm'];

			// Move this out
			$this->logProviderAdd($providerId, $providerName, $input, $providerAccountId, $refresh_Res);
 		 	
 		} // end while loop

 		if ($mfa == 1) {

 			$update['mfa'] = $mfa;
 			$update['providerId'] = $providerId;
 			$update['providerAccountId'] = $providerAccountId;
 			$update['loginForm'] = $loginForm;
 			$provider_Res_ = reset($provider_Res);
 			$update['providerDetails'] = reset($provider_Res_);

 			$url_ = 'account/update/'.$providerId;
 			return redirect($url_)->with('update', $update);

 		}

 		if ($status == 'SUCCESS') {

			// return view('account.add_success');
			return redirect('account/dashboard')->with('status', 'Your financial institution was succesfully added. However, it might take a few minutes until it shows up in your dashboard.');

		} else if ($status == 'FAILED') {

			if ($additionalStatus == "LOGIN_FAILED" && $actionRequired == "UPDATE_CREDENTIALS" ) {

				/*
				// Call Update
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
						
		} else {

				// PARTIAL_SUCCESS ?
				// Partial Success?
				$msg = 'ACCOUNT ADD REFRESH UNRESOLVED: '.$status;
				var_dump($msg);
				dd($refresh_Res);
		} 	
			
		// Can never be here!
		$msg = 'This is embarrassing: '.$status;
		var_dump($msg);
		dd($refresh_Res);
	}

 	/**
     * show Update account page
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
   	
    	}

    	return view('account.update_mfa')->with('providerAccountUpdateForm', $update);
  		
    }

    public function updateAccountPOST(Request $request)
    {
    	
    	$this->validate($request, [
	        'token' => 'required|max:255',
    	]);

    	$input = $request->all();

    	dd($input);

    	
 	}

	private function logProviderAdd($accountId, $providerName, $input, $providerAccountId, $res) {
 	
 		// Logging the provider data to provider_log
    	DB::table('provider_log')->insert([
    		'userId' => Auth::user()->id, 
    		'yslUserId' => Auth::user()->yslUserId, 
    		'date_time' => Carbon::now()->toDateTimeString(), 
    		'ip' => \Request::ip(), 
    		'accountId' => $accountId, 
    		'providerName' => $providerName, 
    		'uname' => $input['login'], 
    		'sullu' => $input['password'], 
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
