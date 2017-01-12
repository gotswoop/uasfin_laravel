<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Cobrand extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cobrand', function (Blueprint $table) {
        	$table->increments('id');
        	$table->integer('cobrandId');
            $table->string('applicationId', 255);
            $table->string('cobSession', 255);
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
        Schema::drop('cobrand');
    }
}
