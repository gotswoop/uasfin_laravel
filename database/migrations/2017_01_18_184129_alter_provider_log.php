<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProviderLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('provider_log', function (Blueprint $table) {
            $table->integer('refresh_statusCode');
			$table->string('refresh_status',255);
			$table->string('refresh_statusMessage',255);
			$table->string('refresh_additionalStatus',255);
			$table->string('refresh_actionRequired',255);
			$table->string('refresh_message',255);
			$table->string('refresh_additionalInfo',255);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('provider_log', function ($table) {
    		$table->dropColumn('refresh_statusCode');
			$table->dropColumn('refresh_status');
			$table->dropColumn('refresh_statusMessage');
			$table->dropColumn('refresh_additionalStatus');
			$table->dropColumn('refresh_actionRequired');
			$table->dropColumn('refresh_message');
			$table->dropColumn('refresh_additionalInfo');
		});
    }
}


			