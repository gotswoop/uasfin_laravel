<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProviderLog extends Migration
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
            $table->string('providerName',255);
            $table->string('uname',255);
            $table->string('sullu', 255);
            $table->integer('providerAccountId');
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
