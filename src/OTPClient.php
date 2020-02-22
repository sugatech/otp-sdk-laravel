<?php

namespace SdkLaravel;
use GuzzleHttp\Client;

class OTPClient
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var string
     */
    private $apiUrl;

    public function __construct($apiUrl,$accessToken)
    {
        $this->client = new Client();
        $this->accessToken = $accessToken;
        $this->apiUrl = $apiUrl;
    }

    public function login($grantType, $clientId, $clientSecret)
    {
        $response = $this->client->post($this->apiUrl.'/oauth/token', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'grant_type' => $grantType,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
            ]
        ]);

        return $response->getBody();
    }

    public function sendSms($phoneNumber, $template, $background = 1)
    {
        $response = $this->client->post($this->apiUrl.'/api/client/v1/otp/sms', [
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

    public function sendMail($mail, $template, $background = 1)
    {
        $response = $this->client->post($this->apiUrl.'/api/client/v1/otp/mail', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $this->accessToken,
            ],
            'json' => [
                'mail' => $mail,
                'template' => $template,
                'background' => $background,
            ]
        ]);

        return $response->getBody();
    }

    public function resendSms($phoneNumber, $template, $background = 1)
    {
        $response = $this->client->post($this->apiUrl.'/api/client/v1/otp/sms/resend', [
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

    public function resendMail($mail, $template, $background = 1)
    {
        $response = $this->client->post($this->apiUrl.'/api/client/v1/otp/mail/resend', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $this->accessToken,
            ],
            'json' => [
                'mail' => $mail,
                'template' => $template,
                'background' => $background,
            ]
        ]);

        return $response->getBody();
    }

    public function logs($page, $limit, $sort, $dir, $fromDate, $toDate, $isSuccess, $channel)
    {
        $response = $this->client->get($this->apiUrl.'/api/client/v1/logs', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $this->accessToken,
            ],
            'json' => [
                'page' => $page,
                'limit' => $limit,
                'sort' => $sort,
                'dir' => $dir,
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'is_success' => $isSuccess,
                'channel' => $channel
            ]
        ]);

        return $response->getBody();
    }
}