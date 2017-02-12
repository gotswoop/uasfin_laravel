<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserAccountTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_account_transactions', function (Blueprint $table) {
        	$table->increments('id');
	        $table->integer('userId');
	        $table->integer('providerId');
			$table->string('providerName');
			$table->integer('providerAccountId');
			$table->integer('accountId');
			$table->integer('transactionId');
			$table->string('accountName')->nullable();
			$table->char('accountType', 100)->nullable();
			$table->char('container', 25)->nullable();
			$table->char('accountStatus', 100)->nullable();
			$table->boolean('isAsset')->nullable()->nullable();
			$table->decimal('balance_amount', 19, 4)->nullable();
			$table->char('balance_currency', 4)->nullable();
			$table->decimal('balance_daily_amount', 19, 4)->nullable();
			$table->char('balance_daily_currency', 4)->nullable();
			$table->decimal('balance_rolling_amount', 19, 4)->nullable();
			$table->char('balance_rolling_currency', 4)->nullable();
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
			$table->char('refresh_statusMessage',25)->nullable();
			$table->char('refresh_lastRefreshed', 25)->nullable();
			$table->char('refresh_lastRefreshAttempt', 25)->nullable();
			$table->char('refresh_nextRefreshScheduled', 25)->nullable();
			$table->char('trans_baseType', 10)->nullable();
			$table->decimal('trans_amount', 19, 4)->nullable();
			$table->char('trans_currency', 4)->nullable();
			$table->char('trans_categoryType', 50)->nullable();
			$table->integer('trans_categoryId')->nullable();
			$table->string('trans_category')->nullable();
			$table->char('trans_categorySource', 25)->nullable();
			$table->string('trans_description_original')->nullable();
			$table->string('trans_description_simple')->nullable();
			$table->string('trans_type')->nullable();
			$table->decimal('trans_principal_amount', 19, 4)->nullable();
			$table->char('trans_principal_currency', 4)->nullable();
			$table->dateTime('trans_date')->nullable()->nullable();
			$table->dateTime('trans_transactionDate')->nullable();
			$table->dateTime('trans_postDate')->nullable()->nullable();
			$table->char('trans_status', 20)->nullable();
			$table->integer('trans_merchant_id')->nullable();
			$table->string('trans_merchant_name')->nullable();
			$table->integer('trans_highLevelCategoryId')->nullable();
			$table->text('raw_transaction_data')->nullable();
			$table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
			$table->unique(['userId', 'providerId', 'providerAccountId', 'accountId', 'transactionId'], 'user_pid_paid_aid_tid');
    	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_account_transactions');
    }
}

	
	

