<?php

namespace App\Library\Yodlee;

use GuzzleHttp;

class Utils {

	static function httpGet ( $requestUrl, $cobrandSession, $userSession ) 
	{

		$client = new GuzzleHttp\Client(['http_errors' => false]);
	        
		if (!empty($cobrandSession)) {
		   $auth = '{cobSession='.$cobrandSession.'}';
		}
		if (!empty($cobrandSession) && !empty($userSession)) {
		   $auth = '{cobSession='.$cobrandSession.', userSession='.$userSession.'}';
		}

	    $res = $client->request('GET', $requestUrl, [
	        'headers' => [ 'Authorization' => $auth ],
	    ]);

	    $response = self::parseResponse($res);

	    return $response;
    
	}

	static function httpPost ( $requestUrl, $params, $cobrandSession, $userSession ) 
	{

		$client = new GuzzleHttp\Client(['http_errors' => false]);
	        
		$auth = null;
		if (!empty($cobrandSession)) {
		   $auth = '{cobSession='.$cobrandSession.'}';
		}
		if (!empty($cobrandSession) && !empty($userSession)) {
		   $auth = '{cobSession='.$cobrandSession.', userSession='.$userSession.'}';
		}

		$res = $client->request('POST', $requestUrl , [
			'headers' => [ 'Authorization' => $auth ],
            'form_params' => $params,
        ]);

        return self::parseResponse($res);
	}


	static function parseResponse ( $res ) {

		$web['httpStatus'] = $res->getStatusCode();
		$web['body'] = json_decode($res->getBody(), true);
		$web['error'] = [];
		if ($web['httpStatus'] != "200") {
			$web['error']['code'] = $web['body']['errorCode'];
			$web['error']['message'] = $web['body']['errorMessage'];
			if ( isset($web['body']['referenceCode']) ) {
				$web['error']['referenceCode'] = $web['body']['referenceCode'];	
			}
			$web['body'] = '';
		}

		return $web;
	}

	static function readPublicKey() {

		$keyFile = fopen(__DIR__.'/../keys/PublicKey.txt', "r") or die("Unable to open file!");
		$fileData = fread($keyFile,filesize(__DIR__.'/../keys/PublicKey.txt'));
		fclose($keyFile);
		$resObj = json_decode($fileData,true);
		$publicKey = $resObj['keyAsPemString'];
		return $publicKey;

	}

	static function encryptData($plainText, $publicKey) {
 
		openssl_public_encrypt($plainText, $encrypted, $publicKey, OPENSSL_PKCS1_PADDING);
		return bin2hex($encrypted);
	}

}
