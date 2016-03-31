<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstName');
            $table->string('lastName');
            $table->string('email')->unique();
            $table->string('password', 60);
            $table->string('suddi', 100);
            $table->dateTime('join_date');
            $table->dateTime('last_login_date');
            $table->string('regIP', 15);
            $table->string('salt', 32);
            $table->tinyInteger('comminication');
            $table->tinyInteger('usertype');
            $table->tinyInteger('access');
            $table->binary('settings');
            $table->integer('yslUserId');
            $table->string('yslUserSessionToken', 255);
            $table->string('yslCobrandSessionToken', 255);
            $table->dateTime('yslUserSessionToken_date');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
