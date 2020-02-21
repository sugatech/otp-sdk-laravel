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

    /**
     * @var string
     */
    private $accessToken;

    public function __construct($accessToken)
    {
        $this->client = new Client();
        $this->accessToken = $accessToken;
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

    public function sendSms($phoneNumber, $template, $background = 1)
    {
        $response = $this->client->get(self::API_URI.'/api/v1/logs', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $this->accessToken,
            ],
            'json' => [
                'phone_number' => $phoneNumber,
                'template' => $template,
                'background' => $background,
            ]
        ]);

        return $response->getBody();
    }
}