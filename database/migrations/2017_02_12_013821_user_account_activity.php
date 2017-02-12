<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserAccountActivity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	Schema::create('user_account_activity', function (Blueprint $table) {
	        $table->increments('id');
	        $table->integer('userId');
            $table->integer('yslUserId')->nullable();
            $table->string('ip', 15)->nullable();
            $table->integer('providerAccountId');
            $table->integer('accountId')->nullable();
            $table->char('action', 25);
            $table->char('action_details', 50);
	        $table->timestamp('action_dateTime')->default(DB::raw('CURRENT_TIMESTAMP'));
    	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_account_activity');
    }
}
