<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'panelId', 'treatment', 'firstName', 'lastName', 'email', 'password', 'suddi', 'join_date', 'last_login_date', 'regIP', 'salt', 'communication', 'usertype', 'access', 'settings', 'yslUserId', 'yslUserSessionToken', 'yslCobrandSessionToken', 'yslUserSessionToken_date',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'suddi', 'remember_token'
    ];


    /** 
     * Date fields added to Carbon
     *
     */
    protected $dates = [
        'join_date', 'last_login_date', 'yslUserSessionToken_date',
    ];

    public static function getSession($user_id)
    {
    	// returning user session that's less than 25 minutes (1500 seconds) old
    	$sql = 'SELECT id, yslUserSessionToken, yslUserSessionToken_date FROM users WHERE UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(yslUserSessionToken_date) < 1500 AND id = '. $user_id;

    	$result = \DB::select($sql);

    	$result = reset($result); // only need the first element of the array $result[0] which is a StdClass Object

    	return (array) $result; //converting the stdClass object to array
    }
}