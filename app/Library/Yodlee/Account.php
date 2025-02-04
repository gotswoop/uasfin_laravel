<?php 

namespace App\Library\Yodlee;

use Auth;
use Carbon\Carbon;

class Account {

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

	/**
     * Get all accounts that belong to a user to show on dashboard
     * FROM AccountController -> dashboard()
     * YSL URL (GET): https://usyirestmaster.api.yodlee.com/ysl/uscnew/v1/accounts
     */ 
	public function getAllAccounts() 
	{

		if ( $this->yodleeUser->isActive() ) { // Checking if user is active

			$request = config('services.yodlee.accounts.url');

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
					'event' => 'Getting all accounts for user', 
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
     * Get a summary of the account to display on the individual account details page
     * FROM AccountController -> details()
	 * YSL URL (GET): https://usyirestmaster.api.yodlee.com/ysl/uscnew/v1/accounts/10683262?container=creditCard
     */
	public function getSummary($accountId, $container) 
	{

		if ( $this->yodleeUser->isActive() ) { // Checking if user is active

			// TODO: Clean input ($accoundId)
			$request = config('services.yodlee.accounts.url'). '/' . $accountId.'?container='.$container;
			
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
					'event' => 'Getting summary for user', 
					'params' => $accountId.'?container='.$container, 
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
     * Get a list of all transactions for the account to display on the individual account details page
     * FROM AccountController -> details()
     * YSL URL (GET): https://usyirestmaster.api.yodlee.com/ysl/uscnew/v1/transactions/?accountId=10683262
	 */
    public function getTransactions($accountId) 
	{
		if ( $this->yodleeUser->isActive() ) { // Checking if user is active

			// TODO: Clean input ($accoundId) see if it's valid?

			$request = config('services.yodlee.transactions.url');

			$queryArgs = array();
			$queryArgs['accountId'] = $accountId;
			// Showing last 60 days of transactions. Default is 30
			$queryArgs['fromDate'] = Carbon::now()->subDays(60)->toDateString();
			$queryArgs['toDate'] = Carbon::now()->toDateString();
			
			if(count($queryArgs) > 0) {
            	$request = $request.'?'.http_build_query($queryArgs, '', '&');
			}
			
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
					'event' => 'Getting all transactions for user', 
					'params' => $accountId, 
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

    // delete a specific account within a providerAccountId
    public function deleteAccount($accountId) {

    	if ( $this->yodleeUser->isActive() ) { // Checking if user is active

			$request = config('services.yodlee.accounts.url').'/'.$accountId;

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
					'event' => 'Getting an Yodlee Account (inside providerAccountId)', 
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

    ########################
    ##	NOT IN USE YET
    ########################
    public function getNetWorth() 
	{

		if ( $this->yodleeUser->isActive() ) { // Checking if user is active
			
			$request = config('services.yodlee.netWorthUrl').'?accoundIds=10050740';

			$responseObj = Utils::httpGet($request, Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken);

			if ( $responseObj['httpStatus'] == '200' ) {

				return $responseObj['body'];

			} else {

				$err = array('file' => __FILE__, 'method' => __FUNCTION__, 'event' => 'Getting net worth for user'); 
				$error = array_merge($err, $responseObj['error']);

			}

		} else {

			return false;

		}
    }

}