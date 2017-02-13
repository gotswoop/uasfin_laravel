<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsers extends Migration
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
            $table->integer('panelId');
            $table->integer('treatment')
            $table->string('firstName');
            $table->string('lastName');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('suddi');
            $table->dateTime('join_date');
            $table->dateTime('last_login_date');
            $table->string('regIP', 15);
            $table->string('salt', 32);
            $table->tinyInteger('comminication');
            $table->tinyInteger('usertype');
            $table->tinyInteger('access');
            $table->binary('settings');
            $table->integer('yslUserId');
            $table->string('yslUserSessionToken');
            $table->string('yslCobrandSessionToken');
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
