<?php

namespace OTP\SdkLaravel;
use GuzzleHttp\Client;

class OTPClient
{
    /**
     * @const The API URL for OTP service
     */
    const API_URI = 'http://service-loc-otp.sugadev.top';

    /**
     * @var Client
     */
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function login($grantType, $clientId, $clientSecret)
    {
        $response = $this->client->post(self::API_URI.'/oauth/token', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'json' => [
                    'grant_type' => $grantType,
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                ]
            ]
        ]);

        return $response->getBody();
    }
}