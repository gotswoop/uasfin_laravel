<?php 

namespace App\Library\Yodlee;

use Auth;

class Account {

	public function getNetWorth() // NOT IN USE
	{

		$request = config('services.yodlee.netWorthUrl').'?accoundIds=10050740';

		$responseObj = Utils::httpGet($request, Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken);

		if ( $responseObj['httpStatus'] == '200' ) {
			return $responseObj['body'];
		} else {
			dd($responseObj['error']);
		}
    }

	public function getSummary($accountId, $container) 
	{

		// SWOOP: Clean input ($accoundId)
		$request = config('services.yodlee.accounts.url'). '/' . $accountId.'?container='.$container;

		$responseObj = Utils::httpGet($request, Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken);

		if ( $responseObj['httpStatus'] == '200' ) {
			return $responseObj['body'];
		} else {
			dd($responseObj['error']);
		}
    }

    public function getTransactions($accountId) 
	{

		// SWOOP: Clean input ($accoundId)
		$request = config('services.yodlee.transactions.url'). '?accountId=' . $accountId;

		$responseObj = Utils::httpGet($request, Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken);

		if ( $responseObj['httpStatus'] == '200' ) {
			return $responseObj['body'];
		} else {
			dd($responseObj['error']);
		}
    }

    /**
     * Get all accounts that belong to a user
     */
	public function getAllAccounts() 
	{

		$request = config('services.yodlee.accounts.url');

		$responseObj = Utils::httpGet($request, Auth::user()->yslCobrandSessionToken, Auth::user()->yslUserSessionToken);

		if ( $responseObj['httpStatus'] == '200' ) {
			return $responseObj['body'];
		} else {
			dd($responseObj['error']);
		}
    }

}