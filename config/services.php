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
        'webhookKey' => env('MAILGUN_WEBHOOK_KEY'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT'),
        'GEO_CODE_TTL' => env('GEO_CODE_TTL', 600),
        'GOOGLE_MAP_GEOCODING_KEY' => env('GOOGLE_MAP_GEOCODING_KEY', 600),
    ],
    'zohoCrm' => [
        'client_id' => env('ZOHO_CRM_CLIENT_ID'),
        'client_secret' => env('ZOHO_CRM_CLIENT_SECRET'),
        'redirect' => env('ZOHO_CRM_REDIRECT'),
        'notification' => env('ZOHO_CRM_NOTIFICATION'),
        'zohoBooksOrganizationId' => env('ZOHO_BOOKS_ORGANIZATION_ID'),
        'zohoVerificationString' => env('ZOHO_TOKEN_VERIFICATION_STRING'),
    ],
    'apollo' => [
        'api_key' => env('APOLLO_API_KEY'),
    ],
    'stripe' => [
        'api_key' => env('STRIPE_API_KEY'),
        'api_secret' => env('STRIPE_API_SECRET'),
        'mode' => env('STRIPE_MODE'),
    ],
    'authorizeNet' => [
        'loginId' => env('AUTHORIZE_NET_LOGIN_ID'),
        'transactionKey' => env('AUTHORIZE_NET_TRANSACTION_KEY'),
        'mode' => env('AUTHORIZE_NET_MODE', 'sandbox'),
    ],
];
