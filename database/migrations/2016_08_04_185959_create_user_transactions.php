<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_transactions', function (Blueprint $table) {
        	$table->bigIncrements('id');
			$table->integer('yslUserId');
			$table->integer('panelId');
			$table->integer('institutionId');
		    $table->integer('accountId');
		    $table->integer('transactionId');
			$table->string('container');
			$table->decimal('amount',25,2);
			$table->string('currency');
			$table->string('baseType');
		    $table->string('categoryType');
		    $table->string('categoryId');
		    $table->string('category');
		    $table->string('categorySource');
		    $table->string('merchantName');
		    $table->string('simpleDescription');
		    $table->string('originalDescription');
		    $table->string('isManual');
		    $table->dateTime('yslDate');
		    $table->dateTime('transactionDate');
		    $table->dateTime('postDate');
		    $table->string('status');
		    $table->bigInteger('highLevelCategoryId');
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
        Schema::drop('user_transactions');
    }
}