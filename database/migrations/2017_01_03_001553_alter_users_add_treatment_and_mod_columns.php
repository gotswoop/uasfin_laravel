<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersAddTreatmentAndModColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('treatment')->after('PanelId');
			$table->string('password', 255)->change(); // was 60
			$table->string('suddi', 255)->change(); // was 100
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function ($table) {
    		$table->dropColumn('treatment');
    		$table->string('password',60)->change();
			$table->string('suddi', 100)->change();
		});
    }
}
