<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PagesController extends Controller
{
	public function home (Request $request)
	{
		// echo config('services.yodlee.base_url');
		// echo Config::get('services.yodlee.base_url');
		// dd($request);
		echo $request->name;
		return view('pages.home');

	}
	
	public function contact ()
	{
		return view('pages.contact');
	}
}