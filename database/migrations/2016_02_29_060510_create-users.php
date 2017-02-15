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
            $table->integer('panelId')->nullable();
            $table->string('treatment')->nullable();
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
            $table->tinyInteger('access')->default(1);
            $table->text('settings');
            $table->integer('yslUserId');
            $table->string('yslUserSessionToken')->nullable();
            $table->string('yslCobrandSessionToken')->nullable();
            $table->dateTime('yslUserSessionToken_date')->nullable();
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