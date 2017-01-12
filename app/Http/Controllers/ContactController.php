<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Carbon\Carbon;

use Auth;

class ContactController extends Controller
{

	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function contact()
	{
		return view('pages.contact');
	}

	public function contactSubmit(Request $request)
	{
		$this->validate($request, [
			'firstName'=>'required',
			'lastName'=>'required',
			'email'=>'required',
			'issue'=>'required',
			'details'=>'required',
		]);
    	
    	$input = $request->all();

    	$title = 'UASFIN Support Ticket: '.$request->input('issue');
        $content = "Name: ".$request->input('firstName').' '.$request->input('lastName')."\n".
    			"Email: ".$request->input('email')."\n".
				"User Id: ".Auth::user()->id."\n".
				"Yodlee User Id: ".Auth::user()->yslUserId."\n".
				"IP Address: ".$request->ip()."\n".
				"Issue: ". $request->input('issue')."\n".
		   		"Details: ".$request->input('details')."\n".
		   		"Date/Time: ".Carbon::now()->toDateTimeString();

       	\Mail::raw($content, function($message)
		{
    		   $message->from('noreply@uasfin.usc.edu', 'UASFIN Web (Yodlee)');
         	   $message->to('ssamek@usc.edu', 'Swaroop Yodlee')->subject("UASFIN Support Ticket");
        });

       	\DB::table('support_tickets')->insert(
    		['firstName' => $request->input('firstName'), 'lastName' => $request->input('lastName'), 'email' => $request->input('email'), 'userId' => Auth::user()->id, 'yslUserId' => Auth::user()->yslUserId, 'date_time' => Carbon::now()->toDateTimeString(), 'ip' => \Request::ip(), 'issue' => $request->input('issue'), 'details' => $request->input('details')]
		);

        return view('pages.email_success');
	}
}

