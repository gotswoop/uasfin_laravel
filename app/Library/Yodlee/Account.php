<?php 

namespace App\Library\Yodlee;

use Auth;

class Account {

	public function getDetails($accountId) 
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