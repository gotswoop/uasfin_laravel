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
}

/*
// Created using Migrations
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `firstName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `lastName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `suddi` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `join_date` datetime NOT NULL,
  `last_login_date` datetime NOT NULL,
  `regIP` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `salt` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `comminication` tinyint(4) NOT NULL,
  `usertype` tinyint(4) NOT NULL,
  `access` tinyint(4) NOT NULL,
  `settings` blob NOT NULL,
  `yslUserId` int(11) NOT NULL,
  `yslUserSessionToken` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `yslUserSessionToken_date` datetime NOT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
*/