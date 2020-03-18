<?php

namespace OTP\SDK;

use Zttp\PendingZttpRequest;
use Zttp\Zttp;

class OTPClient
{
    const DEFAULT_TTL = 300;

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
     * @param string $accessToken
     */
    public function __construct($apiUrl, $accessToken)
    {
        $this->accessToken = $accessToken;
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
     * @param string $phoneNumber
     * @param string $template
     * @param bool $background
     * @param int $ttl
     * @return bool
     */
    public function sendSms($phoneNumber, $template, $background = true, $ttl = self::DEFAULT_TTL)
    {
        return $this->request()
            ->asJson()
            ->post($this->getUrl('/otp/sms'),
                [
                    'phone_number' => $phoneNumber,
                    'template' => $template,
                    'background' => $background,
                    'ttl' => $ttl,
                ])
            ->isSuccess();
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
        return $this->request()
            ->asJson()
            ->post($this->getUrl('/otp/mail'),
                [
                    'mail' => $mail,
                    'template' => $template,
                    'background' => $background,
                    'ttl' => $ttl,
                ])
            ->isSuccess();
    }

    /**
     * @param string $phoneNumber
     * @param string $template
     * @param bool $background
     * @return bool
     */
    public function resendSms($phoneNumber, $template, $background = true)
    {
        return $this->request()
            ->asJson()
            ->post($this->getUrl('/otp/sms/resend'),
                [
                    'phone_number' => $phoneNumber,
                    'template' => $template,
                    'background' => $background,
                ])
            ->isSuccess();
    }

    /**
     * @param string $mail
     * @param string $template
     * @param bool $background
     * @return bool
     */
    public function resendMail($mail, $template, $background = true)
    {
        return $this->request()
            ->asJson()
            ->post($this->getUrl('/otp/mail/resend'),
                [
                    'mail' => $mail,
                    'template' => $template,
                    'background' => $background,
                ])
            ->isSuccess();
    }

    /**
     * @param array $params
     * @return object[]
     */
    public function logs($params = [])
    {
        return $this->request()
            ->asJson()
            ->get($this->getUrl('/logs'), $params)
            ->body();
    }

    /**
     * @param string $id
     * @param string $code
     * @return bool
     */
    public function check($id, $code)
    {
        return $this->request()
            ->asJson()
            ->post($this->getUrl('/otp/check'),
                [
                    'id' => $id,
                    'otp_code' => $code,
                ])
            ->isSuccess();
    }

    /**
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        return $this->request()
            ->asJson()
            ->delete($this->getUrl('/otp/'.$key))
            ->isSuccess();
    }
}