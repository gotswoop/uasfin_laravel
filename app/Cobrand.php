<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cobrand extends Model
{
    protected $fillable = [
    	'cobrandId',
    	'applicationId',
    	'cobSession',
    	'session_time',
    ];

    public static function getSession()
    {
    	// returning cobrand session that's less than 70 minutes (4200 seconds) old
    	$result = \DB::select('SELECT id, cobSession, session_time FROM cobrand WHERE UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(session_time) < 4200 ORDER BY id DESC LIMIT 1');

    	$result = reset($result); // only need the first element of the array $result[0] which is a StdClass Object

    	return (array) $result; //converting the stdClass object to array
    }
}
