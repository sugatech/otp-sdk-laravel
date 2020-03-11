<?php

namespace OTP\SDK;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class OTPClient
{
    const DEFAULT_TTL = 300;

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

    /**
     * OTPClient constructor.
     * @param Client $client
     * @param string $apiUrl
     * @param string $accessToken
     */
    public function __construct($client, $apiUrl, $accessToken)
    {
        $this->client = $client;
        $this->accessToken = $accessToken;
        $this->apiUrl = $apiUrl;
    }

    /**
     * @param string $phoneNumber
     * @param string $template
     * @param bool $background
     * @param int $ttl
     * @return \Psr\Http\Message\StreamInterface
     */
    public function sendSms($phoneNumber, $template, $background = true, $ttl = self::DEFAULT_TTL)
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
                'ttl' => $ttl,
            ]
        ]);

        return $response->getBody();
    }

    /**
     * @param string $mail
     * @param string $template
     * @param bool $background
     * @param int $ttl
     * @return \Psr\Http\Message\StreamInterface
     */
    public function sendMail($mail, $template, $background = true, $ttl = self::DEFAULT_TTL)
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
                'ttl' => $ttl,
            ]
        ]);

        return $response->getBody();
    }

    /**
     * @param string $phoneNumber
     * @param string $template
     * @param bool $background
     * @return \Psr\Http\Message\StreamInterface
     */
    public function resendSms($phoneNumber, $template, $background = true)
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

    /**
     * @param string $mail
     * @param string $template
     * @param bool $background
     * @return \Psr\Http\Message\StreamInterface
     */
    public function resendMail($mail, $template, $background = true)
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

    /**
     * @param array $params
     * @return \Psr\Http\Message\StreamInterface
     */
    public function logs($params = [])
    {
        $response = $this->client->get($this->apiUrl.'/api/client/v1/logs', [
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/json',
                'Authorization' => $this->accessToken,
            ],
            RequestOptions::JSON => $params
        ]);

        return $response->getBody();
    }

    /**
     * @param int $id
     * @param string $code
     * @return \Psr\Http\Message\StreamInterface
     */
    public function check($id, $code)
    {
        $response = $this->client->post($this->apiUrl.'/api/client/v1/otp/check', [
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/json',
                'Authorization' => $this->accessToken,
            ],
            RequestOptions::JSON => [
                'id' => $id,
                'otp_code' => $code,
            ]
        ]);

        return $response->getBody();
    }
}