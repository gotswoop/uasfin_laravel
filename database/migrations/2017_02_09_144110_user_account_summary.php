<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserAccountSummary extends Migration
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
			$table->string('accountName');
			$table->char('accountType', 100);
			$table->char('container', 12);
			$table->char('accountStatus', 25);
			$table->boolean('isAsset')->nullable();
			$table->decimal('balance_amount', 19, 4)->nullable();
			$table->char('balance_currency', 3);
			$table->char('lastUpdated', 20);
			$table->boolean('includeInNetWorth');
			$table->decimal('amountDue_amount', 19, 4)->nullable();
			$table->char('amountDue_currency', 3);
			$table->dateTime('amountDue_date');
			$table->decimal('minimumAmountDue_amount', 19, 4)->nullable();
			$table->char('minimumAmountDue_currency', 3);
			$table->decimal('availableCredit_amount', 19, 4)->nullable();
			$table->char('availableCredit_currency', 3);
			$table->decimal('totalCreditLine_amount', 19, 4)->nullable();
			$table->char('totalCreditLine_currency', 3);
			$table->decimal('availableCash_amount', 19, 4)->nullable();
			$table->char('availableCash_currency', 3);
			$table->decimal('totalCashLimit_amount', 19, 4)->nullable();
			$table->char('totalCashLimit_currency', 3);
			$table->decimal('availableBalance_amount', 19, 4)->nullable();
			$table->char('availableBalance_currency', 3);
			$table->decimal('currentBalance_amount', 19, 4)->nullable();
			$table->char('currentBalance_currency', 3);
    		$table->decimal('netWorth_assets', 19, 4)->nullable();
			$table->decimal('netWorth_liabilities', 19, 4)->nullable();
			$table->decimal('netWorth_total', 19, 4)->nullable();
			$table->char('createdDate',20);
			$table->tinyInteger('refresh_statusCode');
			$table->char('refresh_statusMessage',10);
			$table->char('refresh_lastRefreshed', 20);
			$table->char('refresh_lastRefreshAttempt', 20);
			$table->char('refresh_nextRefreshScheduled', 20);
			$table->text('raw_account_data');
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