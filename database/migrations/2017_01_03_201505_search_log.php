<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SearchLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_log', function (Blueprint $table) {
            $table->integer('userId');
            $table->integer('yslUserId');
            $table->dateTime('date_time');
            $table->string('ip', 15);
            $table->string('searchWord', 255);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('search_log');
    }
}
