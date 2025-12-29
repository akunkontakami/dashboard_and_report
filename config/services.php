<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'socket_broadcast' => [
        'url' => env('SOCKET_BROADCAST_URL'),
        'token' => env('SOCKET_BROADCAST_TOKEN'),
    ],


    
    'default_avatar' => 'https://yelow-app-storage.s3.ap-southeast-1.amazonaws.com/cnako525c6GTGkUq1nefIJ38mXinpV5JovDMuuws.png',
    'session-user-prefix' => 'haloyelow-customer',

    'yeastar' => [
        'url' => env('YEASTAR_API_URL'),
        'username' => env('YEASTAR_API_USERNAME'),
        'password' => env('YEASTAR_API_PASSWORD'),
    ],
    
    'API_PBX_URL_V2' => env('API_PBX_URL_V2'),
];
