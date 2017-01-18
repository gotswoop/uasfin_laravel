<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, Mandrill, and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'mandrill' => [
        'secret' => env('MANDRILL_SECRET'),
    ],

    'ses' => [
        'key'    => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'debug' => env('DEBUG'),

    'stripe' => [
        'model'  => App\User::class,
        'key'    => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'yodlee' => [
        'baseUrl' => env('YODLEE_BASE_URL'),
        'refreshUrl' => env('YODLEE_BASE_URL') . env('YODLEE_REFRESH_URL'),
        'netWorthUrl' => env('YODLEE_BASE_URL') . env('YODLEE_USER_NETWORTH'),
        'cobrand' => [
            'login'  => env('YODLEE_COBRAND_LOGIN'),
            'password'  => env('YODLEE_COBRAND_PASSWORD'),
            'loginUrl' => env('YODLEE_BASE_URL') . env('YODLEE_COB_LOGIN_URL'),
            'logoutUrl' => env('YODLEE_BASE_URL') . env('YODLEE_COB_LOGOUT_URL'),
            'sessionToken' => env('YODLEE_COBRAND_SESSION_TOKEN'),
            'publicKeyUrl' => env('YODLEE_BASE_URL') . env('YODLEE_COB_KEY_URL'),
        ],
        'user' => [
            'loginUrl' => env('YODLEE_BASE_URL') . env('YODLEE_USER_LOGIN_URL'),
            'logoutUrl' => env('YODLEE_BASE_URL') . env('YODLEE_USER_LOGOUT_URL'),
            'registerUrl' => env('YODLEE_BASE_URL') . env('YODLEE_USER_REGISTER_URL'),
            'unregisterUrl' => env('YODLEE_BASE_URL') . env('YODLEE_USER_UNREGISTER_URL'),
            'detailsUrl' => env('YODLEE_BASE_URL') . env('YODLEE_USER_DETAILS_URL'),
            'credentialsUrl' => env('YODLEE_BASE_URL') . env('YODLEE_USER_CREDENTIALS_URL'),
            'credentialsTokenUrl' => env('YODLEE_BASE_URL') . env('YODLEE_USER_CREDENTIALS_TOKEN_URL'),
            'currenyPreference' => env('YODLEE_USER_CURRENCY_PREFERENCE'),
            'timezonePreference' => env('YODLEE_USER_TIMEZONE_PREFERENCE'),
            'dateFormatPreference' => env('YODLEE_USER_DATEFORMAT_PREFERENCE'),
            'locale' => env('YODLEE_USER_LOCALE'),
        ],
        'providers' => [
        	'url' => env('YODLEE_BASE_URL') . env('YODLEE_PROVIDER_URL'),
        ],
        'transactions' => [
        	'url' => env('YODLEE_BASE_URL') . env('YODLEE_GET_TRANSACTIONS_URL'),
        ],
        'accounts' => [
        	'url' => env('YODLEE_BASE_URL') . env('YODLEE_GET_ACCOUNTS_URL'),
        ],
        'providerAccounts' => [
        	'url' => env('YODLEE_BASE_URL') . env('YODLEE_PROVIDER_ACCOUNTS_URL'),
        ],
    ],  
];

/*
# DEV
# YODLEE_BASE_URL=https://stage.api.yodlee.com/ysl/private-sandbox89/v1/
# YODLEE_COBRAND_LOGIN=sandbox89
# YODLEE_COBRAND_PASSWORD=Yodlee@123

YODLEE_COBRAND_SESSION_TOKEN=YODLEE_COBRAND_SESSION_TOKEN_NOT_SET

# LIVE
YODLEE_BASE_URL=https://usyirestmaster.api.yodlee.com/ysl/uscnew/v1/
YODLEE_COBRAND_LOGIN=uscnew
YODLEE_COBRAND_PASSWORD=US@Nebfg12@aq1w

YODLEE_COB_LOGIN_URL=cobrand/login
YODLEE_COB_LOGOUT_URL=cobrand/logout
YODLEE_COB_KEY_URL=cobrand/publicKey
YODLEE_USER_LOGIN_URL=user/login
YODLEE_USER_REGISTER_URL=user/register
YODLEE_USER_LOGOUT_URL=user/logout
YODLEE_USER_DETAILS_URL=user
YODLEE_USER_CREDENTIALS_URL=user/credentials
YODLEE_USER_CREDENTIALS_TOKEN_URL=user/credentials/token
YODLEE_USER_UNREGISTER_URL=user/unregister
YODLEE_PROVIDER_URL=providers
YODLEE_GET_ACCOUNTS_URL=accounts
YODLEE_GET_PORTFOLIO_URL=portfolio/assetSummary
YODLEE_GET_ACCOUNTS_WITH_INVESTMENT_OPTIONS=accounts/investmentPlan/investmentOptions
YODLEE_GET_TRANSACTIONS_URL=transactions/
YODLEE_GET_TRANSACTIONS_COUNT_URL=transactions/count/
YODLEE_GET_TRANSACTIONS_CATEGORIES_URL=transactions/categories/
YODLEE_GET_HOLDING_TYPES_URL=holdings/holdingType/
YODLEE_GET_HOLDING_URL=holdings/
YODLEE_GET_BILLS_URL=bills/
YODLEE_REFRESH_URL=refresh/
YODLEE_ENABLE_LOGS=1
YODLEE_USER_NETWORTH=derived/networth

YODLEE_USER_CURRENCY_PREFERENCE=USD
YODLEE_USER_TIMEZONE_PREFERENCE=PST
YODLEE_USER_DATEFORMAT_PREFERENCE=MM/DD/YYYY

YODLEE_PROVIDER_ACCOUNTS_URL=providerAccounts

*/
