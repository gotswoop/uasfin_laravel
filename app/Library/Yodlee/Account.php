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
    	$this->yodleeUser = $yodleeUser; // SWOOP: Is this required?
    }

	/**
     * Get all accounts that belong to a user to show on dashboard
     */
	public function getAllAccounts() 
	{

		// Checking if user is active
		if ( $this->yodleeUser->isActive( Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken ) ) {

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

			// Logout user when Yodlee session is inactive
			Auth::Logout();
			return false;
							
		}
    }

    /**
     * Get a summary of the account to display on the individual account details page
     */

	public function getSummary($accountId, $container) 
	{

		// Checking if user is active
		if ( $this->yodleeUser->isActive( Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken ) ) {

			// SWOOP: Clean input ($accoundId)
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

			// Logout user when Yodlee session is inactive
			Auth::Logout();
			return false;

		}
    }

    /**
     * Get a list of all transactions for the account to display on the individual account details page
     */
    public function getTransactions($accountId) 
	{

		// Checking if user is active
		if ( $this->yodleeUser->isActive( Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken ) ) {
			// SWOOP: Clean input ($accoundId)
			$request = config('services.yodlee.transactions.url'). '?accountId=' . $accountId;

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

			// Logout user when Yodlee session is inactive
			Auth::Logout();
			return false;	

		}
    }

    




    // ------------------------------  NOT IN USE YET

    public function getNetWorth() 
	{

		// Checking if user is active
		if ( $this->yodleeUser->isActive( Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken ) ) {

			$request = config('services.yodlee.netWorthUrl').'?accoundIds=10050740';

			$responseObj = Utils::httpGet($request, Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken);

			if ( $responseObj['httpStatus'] == '200' ) {

				return $responseObj['body'];

			} else {

				$err = array('file' => __FILE__, 'method' => __FUNCTION__, 'event' => 'Getting net worth for user'); 
				$error = array_merge($err, $responseObj['error']);
				dd($error);

			}

		} else {

			// Logout user when Yodlee session is inactive
			Auth::Logout();
			return false;

		}
    }

}