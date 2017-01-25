<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PagesController extends Controller
{
	public function home (Request $request)
	{
	
		// If a user is logged in, redirect to dashboard instead of home page
		if ( $request->user() ) {

			return redirect()->action('AccountController@dashboard');

        } else {

			return view('pages.home');
			
		}
	}

	

}