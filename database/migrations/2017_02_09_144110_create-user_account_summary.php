<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAccountSummary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_account_summary', function (Blueprint $table) {
			$table->increments('id');
	        $table->integer('userId');
	        $table->integer('providerId');
			$table->string('providerName');
			$table->integer('providerAccountId');
			$table->integer('accountId');
			$table->string('accountName')->nullable();
			$table->char('accountType', 100)->nullable();
			$table->char('container', 50)->nullable();
			$table->char('accountStatus', 100)->nullable();
			$table->boolean('isAsset')->nullable();
			$table->decimal('balance_amount', 19, 4)->nullable();
			$table->char('balance_currency', 4)->nullable();
			$table->char('lastUpdated', 25)->nullable();
			$table->boolean('includeInNetWorth')->nullable();
			$table->decimal('amountDue_amount', 19, 4)->nullable();
			$table->char('amountDue_currency', 4)->nullable();
			$table->dateTime('amountDue_date')->nullable();
			$table->decimal('minimumAmountDue_amount', 19, 4)->nullable();
			$table->char('minimumAmountDue_currency', 4)->nullable();
			$table->decimal('availableCredit_amount', 19, 4)->nullable();
			$table->char('availableCredit_currency', 4)->nullable();
			$table->decimal('totalCreditLine_amount', 19, 4)->nullable();
			$table->char('totalCreditLine_currency', 4)->nullable();
			$table->decimal('availableCash_amount', 19, 4)->nullable();
			$table->char('availableCash_currency', 4)->nullable();
			$table->decimal('totalCashLimit_amount', 19, 4)->nullable();
			$table->char('totalCashLimit_currency', 4)->nullable();
			$table->decimal('availableBalance_amount', 19, 4)->nullable();
			$table->char('availableBalance_currency', 4)->nullable();
			$table->decimal('currentBalance_amount', 19, 4)->nullable();
			$table->char('currentBalance_currency', 4)->nullable();
    		$table->decimal('netWorth_assets', 19, 4)->nullable();
			$table->decimal('netWorth_liabilities', 19, 4)->nullable();
			$table->decimal('netWorth_total', 19, 4)->nullable();
			$table->char('createdDate', 25)->nullable();
			$table->integer('refresh_statusCode')->nullable();
			$table->string('refresh_statusMessage')->nullable();
			$table->char('refresh_lastRefreshed', 25)->nullable();
			$table->char('refresh_lastRefreshAttempt', 25)->nullable();
			$table->char('refresh_nextRefreshScheduled', 25)->nullable();
			$table->text('raw_account_data')->nullable();
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
			$table->unique(['userId', 'providerId', 'providerAccountId', 'accountId'], 'user_pid_paid_aid');
    	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_account_summary');
    }
}