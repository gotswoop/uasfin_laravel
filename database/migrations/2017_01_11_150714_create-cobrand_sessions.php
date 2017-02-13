<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCobrandSessions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cobrand_sessions', function (Blueprint $table) {
        	$table->increments('id');
        	$table->integer('cobrandId');
            $table->string('applicationId');
            $table->string('cobSession');
            $table->dateTime('session_time');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cobrand_sessions');
    }
}
