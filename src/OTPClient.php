<?php

namespace OTP\SDK;

use PassportClientCredentials\OAuthClient;
use Zttp\PendingZttpRequest;
use Zttp\Zttp;
use Zttp\ZttpResponse;

class OTPClient
{
    const DEFAULT_TTL = 300;

    /**
     * @var OAuthClient
     */
    private $oauth;

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
     * @param string $apiUrl
     */
    public function __construct($apiUrl)
    {
        $this->oauth = new OAuthClient(
            config('otp.oauth.url'),
            config('otp.oauth.client_id'),
            config('otp.oauth.client_secret')
        );
        $this->accessToken = $this->oauth->getAccessToken();
        $this->apiUrl = $apiUrl;
    }

    /**
     * @return PendingZttpRequest
     */
    private function request()
    {
        return Zttp::withHeaders([
            'Authorization' => 'Bearer ' . $this->accessToken,
        ])
            ->withoutVerifying();
    }

    /**
     * @param string $route
     * @return string
     */
    private function getUrl($route)
    {
        return $this->apiUrl . '/api/client/v1' . $route;
    }

    /**
     * @param ZttpResponse $response
     */
    private function validateAccessToken($response)
    {
        if ($response->status() == 401) {
            $this->oauth->getAccessToken(true);
        }
    }

    /**
     * @param string $phoneNumber
     * @param string $template
     * @param bool $background
     * @param int $ttl
     * @return bool
     */
    public function sendSms($phoneNumber, $template, $background = true, $ttl = self::DEFAULT_TTL)
    {
        $response = $this->request()
            ->asJson()
            ->post($this->getUrl('/otp/sms'),
                [
                    'phone_number' => $phoneNumber,
                    'template' => $template,
                    'background' => $background,
                    'ttl' => $ttl,
                ]);

        $this->validateAccessToken($response);

        return $response->isSuccess();
    }

    /**
     * @param string $mail
     * @param string $template
     * @param bool $background
     * @param int $ttl
     * @return bool
     */
    public function sendMail($mail, $template, $background = true, $ttl = self::DEFAULT_TTL)
    {
        $response = $this->request()
            ->asJson()
            ->post($this->getUrl('/otp/mail'),
                [
                    'mail' => $mail,
                    'template' => $template,
                    'background' => $background,
                    'ttl' => $ttl,
                ]);

        $this->validateAccessToken($response);

        return $response->isSuccess();
    }

    /**
     * @param string $phoneNumber
     * @param string $template
     * @param bool $background
     * @return bool
     */
    public function resendSms($phoneNumber, $template, $background = true)
    {
        $response = $this->request()
            ->asJson()
            ->post($this->getUrl('/otp/sms/resend'),
                [
                    'phone_number' => $phoneNumber,
                    'template' => $template,
                    'background' => $background,
                ]);

        $this->validateAccessToken($response);

        return $response->isSuccess();
    }

    /**
     * @param string $mail
     * @param string $template
     * @param bool $background
     * @return bool
     */
    public function resendMail($mail, $template, $background = true)
    {
        $response = $this->request()
            ->asJson()
            ->post($this->getUrl('/otp/mail/resend'),
                [
                    'mail' => $mail,
                    'template' => $template,
                    'background' => $background,
                ]);

        $this->validateAccessToken($response);

        return $response->isSuccess();
    }

    /**
     * @param array $params
     * @return array[]
     */
    public function logs($params = [])
    {
        $response = $this->request()
            ->asJson()
            ->get($this->getUrl('/logs'), $params);

        $this->validateAccessToken($response);

        return $response->json();
    }

    /**
     * @param string $id
     * @param string $code
     * @return bool
     */
    public function check($id, $code)
    {
        $response = $this->request()
            ->asJson()
            ->post($this->getUrl('/otp/check'),
                [
                    'id' => $id,
                    'otp_code' => $code,
                ]);

        $this->validateAccessToken($response);

        return $response->isSuccess();
    }

    /**
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        $response = $this->request()
            ->asJson()
            ->delete($this->getUrl('/otp/'.$key));

        $this->validateAccessToken($response);

        return $response->isSuccess();
    }
}