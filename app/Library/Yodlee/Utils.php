<?php

namespace App\Library\Yodlee;

use GuzzleHttp;

class Utils {


	static function httpGet ( $requestUrl, $cobrandSession, $userSession = NULL, $payload = NULL )
	{

		$client = new GuzzleHttp\Client(['http_errors' => false]);
	        
		$auth = '{';    
		if (!empty($cobrandSession)) {
		   $auth .= 'cobSession='.$cobrandSession;
		}
		if (!empty($userSession)) {
		   $auth .= ', userSession='.$userSession;
		}
		if (!empty($payload)) {
		   $auth .= ', '.$payload;
		}
		$auth .= '}';

		/*
		if (!empty($cobrandSession)) {
		   $auth = '{cobSession='.$cobrandSession.'}';
		}
		if (!empty($cobrandSession) && !empty($userSession)) {
		   $auth = '{cobSession='.$cobrandSession.', userSession='.$userSession.'}';
		}
		*/

		$res = $client->request('GET', $requestUrl, [
			'headers' => [ 'Authorization' => $auth ],
			'version' => 1.0,
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
			'version' => 1.0,
		]);

		/*
		$res = $client->request('POST', $requestUrl , [
			'headers' => [ 'Authorization' => $auth, 'Content-type' => 'application/json'],
			'form_params' => $params,
		]);
		*/

        return self::parseResponse($res);
	}

	static function httpDelete ( $requestUrl, $params, $cobrandSession, $userSession ) 
	{

		$client = new GuzzleHttp\Client(['http_errors' => false]);
	        
		$auth = null;
		if (!empty($cobrandSession)) {
		   $auth = '{cobSession='.$cobrandSession.'}';
		}
		if (!empty($cobrandSession) && !empty($userSession)) {
		   $auth = '{cobSession='.$cobrandSession.', userSession='.$userSession.'}';
		}

		$res = $client->request('DELETE', $requestUrl , [
			'headers' => [ 'Authorization' => $auth ],
			'form_params' => $params,
			'version' => 1.0,
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


	static function parseJson($json) {
		return json_decode($json,true);
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


	static function httpPostCurl($request, $postargs, $cobSession, $userSession) {
	   $auth = null;
	   if (!empty($cobSession)) {
		   $auth="{cobSession=".$cobSession."}";
	   }
	   if (!empty($cobSession) && !empty($userSession)) {
		   $auth="{cobSession=".$cobSession.",userSession=".$userSession."}";
	   }
	  
	   $session = curl_init($request);
	   curl_setopt ($session, CURLOPT_POST, true); 
	   curl_setopt ($session, CURLOPT_POSTFIELDS, $postargs); 
	   curl_setopt($session, CURLOPT_HTTPHEADER, array('Authorization:'.$auth,'Content-type: application/json'));
	   curl_setopt($session, CURLOPT_HEADER, true); 
	   curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
	   curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
	   
	   if (!empty($GLOBALS['HTTP_PROXY'])) curl_setopt($session, CURLOPT_PROXY, $GLOBALS['HTTP_PROXY']); 
	   $response = curl_exec($session); 
	
	   $header_size = curl_getinfo($session, CURLINFO_HEADER_SIZE);
	   $headers = self::get_headers_from_curl_response($response);
	   $body = substr($response, $header_size);
	   $httpcode = curl_getinfo($session, CURLINFO_HTTP_CODE);
	   
	   curl_close($session); 
	   $details["httpStatus"]=$httpcode;
	   $details["body"]=$body;
	   $details["headers"]=$headers;

	   return $details;
	}

	static function get_headers_from_curl_response($response)
	{
		$headers = array();
        $links = array();
		$header_text = substr($response, 0, strpos($response, "\r\n\r\n"));

		foreach (explode("\r\n", $header_text) as $i => $line)
			if ($i === 0)
				$headers['http_code'] = $line;
			else
			{
				list ($key, $value) = explode(': ', $line);
				if($key == "Link") {
					//echo "This is Link Headerss...".PHP_EOL;
					$linksSize = count($links);
					//echo "linksSize...".$linksSize.PHP_EOL;
					//if(count($links)) $links[$linksSize++] = $value;
					list($k,$v) = explode(';', $value);
					$links[$v]=$k;
					$headers[$key] = $links;
				} else {
					$headers[$key] = $value;	
				}
			}
		return $headers;
	}

}
