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

        // SWOOP: What if there are no account in dashboard. Show add account.
        if (isset($dashboard['account'])) {
	        return view('account.dashboard')->with('accounts', $dashboard['account']);
        } else {
        	return \Redirect::to('account/search');
        }
    }

    /**
     * Show details of specific account.
     *
     * @return \Illuminate\Http\Response
     */
    public function details($id = null)
    {
     	$accountDetails = $this->account->getDetails($id);
		return view('account.details')->with('transactions', $accountDetails['transaction']);
        // SWOOP: What if body is empty
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

}