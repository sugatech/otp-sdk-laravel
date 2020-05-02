<?php

return [
    'api_url' => env('OTP_API_URL'),
    'access_token' => env('OTP_ACCESS_TOKEN'),
    'oauth' => [
        'url' => env('OTP_OAUTH_URL', env('OTP_API_URL').'/oauth/token'),
        'client_id' => 1,
        'client_secret' => 'WnkfwnvPTJ0ltu1OiessL0je4YQNMu1vraEIQTZ9',
    ],
];
