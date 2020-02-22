<?php

namespace OTP\SDK;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

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

    public function __construct($client, $apiUrl, $accessToken)
    {
        $this->client = $client;
        $this->accessToken = $accessToken;
        $this->apiUrl = $apiUrl;
    }

    public function sendSms($phoneNumber, $template, $background = 1)
    {
        $response = $this->client->post($this->apiUrl.'/api/client/v1/otp/sms', [
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/json',
                'Authorization' => $this->accessToken,
            ],
            RequestOptions::JSON => [
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
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/json',
                'Authorization' => $this->accessToken,
            ],
            RequestOptions::JSON => [
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
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/json',
                'Authorization' => $this->accessToken,
            ],
            RequestOptions::JSON => [
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
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/json',
                'Authorization' => $this->accessToken,
            ],
            RequestOptions::JSON => [
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
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/json',
                'Authorization' => $this->accessToken,
            ],
            RequestOptions::JSON => [
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