<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
    'facebook' => [
        'client_id' => '1244104445652496',
        'client_secret' => 'b888888a13cc04764f9f9d038a8f504a',
        'redirect' => 'http://localhost:8000/callback',
    ],
    'instagram' => [
        'client_id' => '02de908f106b4f27b1787e17c9a1ce78',
        'client_secret' => '6f72ebe946304e8f880d749112901658',
        'redirect' => 'http://lily.almaiz.net/callback_insta',
    ],

];
