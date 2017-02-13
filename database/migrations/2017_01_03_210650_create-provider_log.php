<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProviderLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
     
		Schema::create('provider_log', function (Blueprint $table) {
            $table->integer('userId');
            $table->integer('yslUserId');
            $table->dateTime('date_time');
            $table->string('ip', 15);
            $table->integer('accountId');
            $table->string('providerName');
            $table->string('uname');
            $table->string('sullu');
            $table->integer('providerAccountId');
            $table->integer('refresh_statusCode');
			$table->string('refresh_status');
			$table->string('refresh_additionalStatus');
			$table->string('refresh_statusMessage');
			$table->string('refresh_actionRequired');
			$table->string('refresh_message');
			$table->string('refresh_additionalInfo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('provider_log');
    }
}
