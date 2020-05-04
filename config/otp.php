<?php

return [
    'api_url' => env('OTP_API_URL'),
    'oauth' => [
        'url' => env('OTP_OAUTH_URL', env('OTP_API_URL').'/oauth/token'),
        'client_id' => env('OTP_OAUTH_CLIENT_ID'),
        'client_secret' => env('OTP_OAUTH_CLIENT_SECRET'),
    ],
];
