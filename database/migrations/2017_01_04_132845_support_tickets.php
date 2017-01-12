<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SupportTickets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
     	Schema::create('support_tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstName', 255);
            $table->string('lastName', 255);
            $table->string('email', 255);
            $table->integer('userId');
            $table->integer('yslUserId');
            $table->dateTime('date_time');
            $table->string('ip', 15);
            $table->string('issue', 255);
            $table->binary('details');
            $table->integer('closed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('support_tickets');
    }
}
