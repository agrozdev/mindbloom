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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'analytics_id' => env('GOOGLE_ANALYTICS_ID'),
        'site_verification' => env('GOOGLE_SITE_VERIFICATION'),
    ],

    'mypos' => [
        'sandbox' => env('MYPOS_SANDBOX', true),
        'config_package' => env('MYPOS_CONFIG_PACKAGE'),
        'sid' => env('MYPOS_SID'),
        'wallet' => env('MYPOS_WALLET_NUMBER'),
        'key_index' => env('MYPOS_KEY_INDEX', 1),
        'private_key_path' => env('MYPOS_PRIVATE_KEY_PATH', storage_path('app/mypos/private_key.pem')),
        'public_key_path' => env('MYPOS_PUBLIC_KEY_PATH', storage_path('app/mypos/mypos_public_key.pem')),
    ],

];
