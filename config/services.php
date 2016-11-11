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

    'northstar' => [
        'grant' => 'client_credentials',
        'url' => env('NORTHSTAR_URL'),
        'client_credentials' => [
            'client_id' => env('NORTHSTAR_CLIENT_ID'),
            'client_secret' => env('NORTHSTAR_CLIENT_SECRET'),
            'scope' => ['user', 'admin'],
        ],
    ],

    'mobile_commons' => [
        'username' => env('MOBILE_COMMONS_USERNAME'),
        'password' => env('MOBILE_COMMONS_PASSWORD'),
    ],

];
