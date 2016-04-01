<?php

// Config::set('services.yodlee.cobrand.sessionToken', '');

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
Route::get('/', function () {
	// echo Config::get('services.yodlee.base_url');
	// echo config('services.yodlee.base_url');
    return view('welcome');
});
*/


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => 'web'], function () {
    
    Route::auth();

    Route::get('/', 'PagesController@home');

	Route::get('/account/dashboard', 'AccountController@dashboard');

	Route::get('/account/search', 'AccountController@search');

	Route::post('/account/search', 'AccountController@searchResults');

	Route::get('/account/refresh/{id?}', 'AccountController@refresh');

	Route::get('/account/add/{id}', 'AccountController@add');

	Route::post('/account/add/{id}', 'AccountController@addSubmit');
	
	Route::get('/account/{id}', 'AccountController@details');
	

	/*
	Route::get('/contact', 'PagesController@contact');

	/profile - show profile
	/profile/update - update profile
	
	/accounts/update - accou

	/dashboard
	/dashboard/update - updates the accounts

	/accounts
	/accounts/add

	
	/login
	/register
	/



	*/
	/*
    // Named Routes
	Route::get('/post/{post}', [ 
    	'as' => 'post.get',
    	'uses' => 'PostController@get'
	]);
	
    Route::get('/seed', function (App\Post $post) {
    	$f = \Faker\Factory::create();
    	foreach (range(1, 1000) as $x) {
    		$post->create([
    			'title' => $f->sentence(10),
    			'body' => $f->sentence(100),
    			'live' => "1",
    		]);
    	}
    });

    Route::get('/', 'HomeController@index');
	Route::post('/', 'HomeController@show');

	Route::group(['prefix' => 'account', 'middleware' => 'auth'], function () {
		Route::get('add', function () {
			echo 'account add';
		});
		Route::get('/{id?}', function ($id = null) {
			echo 'account #: '.$id;
		});
	});

	*/
	

});
