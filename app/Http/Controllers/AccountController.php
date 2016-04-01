<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request; // SWOOP
// use Request; // SWOOP

use App\User;
use GuzzleHttp;
use Auth;
use App\Library\Yodlee\Provider;
use App\Library\Yodlee\Account;

class AccountController extends Controller
{
	private $provider; // object
	private $account; // object
	
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Provider $provider, Account $account)
    {
        $this->middleware('auth');
        $this->provider = $provider; // SWOOP: Is this required?
        $this->account = $account; // SWOOP: Is this required?
    }

    /**
     * Show the account dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
		
		$dashboard = $this->account->getAllAccounts();

		// $netWorth = $this->account->getNetWorth(); // Not used
		$netWorth['assets'] = 0;
		$netWorth['liabilities'] = 0;
		foreach ($dashboard['account'] as $account) {
			if ($account['isAsset']) {
				$netWorth['assets'] = $netWorth['assets'] + $account['balance']['amount'];
			} else {
				$netWorth['liabilities'] = $netWorth['liabilities'] + $account['balance']['amount'];

			}
		}
		$netWorth['total'] = $netWorth['assets'] - $netWorth['liabilities'];
		
		// SWOOP: What if there are no account in dashboard. Show add account.
        if (isset($dashboard['account'])) {
	        return view('account.dashboard')->with(array('accounts' => $dashboard['account'], 'netWorth' => $netWorth));
        } else {
        	return \Redirect::to('account/search');
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
     	
     	if (isset($accountDetails['transaction'])) {
	        return view('account.details')->with(array('transactions' => $accountDetails['transaction'], 'summary' => $accountSummary['account'][0]));
        } else {
        	return view('account.details')->with(array('transactions' => null, 'summary' => $accountSummary['account'][0]));
        }
	}

    /**
     * Add an account.
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
    public function add($id = null)
    {
    	
    	$provider = $this->provider->getProviderDetails($id);

    	return view('account.add')->with('providerDetails', $provider['provider']);
    	
    }

    public function addSubmit(Request $request, $id = null)
    {
    	
    	$input = $request->all();

    	$provider = $this->provider->getProviderDetails($id);

    	dd($provider);

		$publicKey = Utils::readPublicKey();
		$loginNameEncrypted = Utils::encryptData($input['login'], $publicKey);
    	$passwordEncrypted = Utils::encryptData($input['password'], $publicKey);
 		$mod_provider = Account::parseAndPopulateProviderDetails($provider, $loginNameEncrypted, $passwordEncrypted);

		// print_r($mod_provider);
		// print PHP_EOL.PHP_EOL;
    	echo $id;
    	print_r($provider);
    	dd($input);

    	$provider = $this->provider->getProviderDetails($id);

    	return view('account.add')->with('providerDetails', $provider);
    	
    }

    /**
     * Refresh a specific provider or all providers that belong to a user
     */
    public function refresh(Request $request, $providerId = null)
    {

    	if ($providerId) {

    		$this->provider->refreshProvider($providerId);
    		// SWOOP: Splash "SUCCESS" before redirecting
    		return \Redirect::to('account/dashboard');

    	} else {

			// get all account belonging to user
			// refresh them and redirect them to dashboard
    		return \Redirect::to('account/dashboard');

    	}
    	

    }

}