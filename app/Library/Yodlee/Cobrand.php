<?php 

namespace App\Library\Yodlee;

use Auth;

class Cobrand {

	public function login() 
	{

		$request = config('services.yodlee.cobrand.loginUrl');
          
        $params = array(
        	'cobrandLogin' => config('services.yodlee.cobrand.login'), 
        	'cobrandPassword' => config('services.yodlee.cobrand.password')
        );

        $responseObj = Utils::httpPost($request, $params, null, null);

		if ( $responseObj['httpStatus'] == '200' ) {
			return $responseObj['body'];
		} else {
			dd($responseObj['error']);
		}
    }

    public function logout() 
    {
    	// SWOOP: 
    }

}