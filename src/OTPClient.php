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
        $this->apiUrl = $apiUrl;
    }

    /**
     * @param callable $handler
     * @return ZttpResponse
     */
    private function request($handler)
    {
        $request = Zttp::withHeaders([
            'Authorization' => 'Bearer ' . $this->oauth->getAccessToken(),
        ])
            ->withoutVerifying();

        $response = $handler($request);

        if ($response->status() == 401) {
            $this->oauth->getAccessToken(true);
        }

        return $response;
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
        $params = [
            'phone_number' => $phoneNumber,
            'template' => $template,
            'background' => $background,
            'ttl' => $ttl,
        ];

        return $this->request(function (PendingZttpRequest $request) use ($params) {
            return $request->asJson()
                ->post($this->getUrl('/otp/sms'), $params);
        })
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
        $params = [
            'mail' => $mail,
            'template' => $template,
            'background' => $background,
            'ttl' => $ttl,
        ];

        return $this->request(function (PendingZttpRequest $request) use ($params) {
            return $request->asJson()
                ->post($this->getUrl('/otp/mail'), $params);
        })
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
        $params = [
            'phone_number' => $phoneNumber,
            'template' => $template,
            'background' => $background,
        ];

        return $this->request(function (PendingZttpRequest $request) use ($params) {
            return $request->asJson()
                ->post($this->getUrl('/otp/sms/resend'), $params);
        })
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
        $params = [
            'mail' => $mail,
            'template' => $template,
            'background' => $background,
        ];

        return $this->request(function (PendingZttpRequest $request) use ($params) {
            return $request->asJson()
                ->post($this->getUrl('/otp/mail/resend'), $params);
        })
            ->isSuccess();
    }

    /**
     * @param array $params
     * @return array[]
     */
    public function logs($params = [])
    {
        return $this->request(function (PendingZttpRequest $request) use ($params) {
            return $request->asJson()
                ->get($this->getUrl('/logs'), $params);
        })
            ->json();
    }

    /**
     * @param string $id
     * @param string $code
     * @return bool
     */
    public function check($id, $code)
    {
        $params = [
            'id' => $id,
            'otp_code' => $code,
        ];

        return $this->request(function (PendingZttpRequest $request) use ($params) {
            return $request->asJson()
                ->post($this->getUrl('/otp/check'), $params);
        })
            ->isSuccess();
    }

    /**
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        return $this->request(function (PendingZttpRequest $request) use ($key) {
            return $request->asJson()
                ->delete($this->getUrl('/otp/' . $key));
        })
            ->isSuccess();
    }
}