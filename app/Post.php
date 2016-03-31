<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'posts'; // for legacy tables where the table name is different

    protected $fillable = [
    	'title',
    	'body',
    ];


/*
    public function getRouteKeyName() 
    {
    	return 'slug';
    }
    */
}
