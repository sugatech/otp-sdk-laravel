<?php

namespace OTP\SDK;

use OAuth2ClientCredentials\OAuthClient;
use Zttp\PendingZttpRequest;
use Zttp\Zttp;
use Zttp\ZttpResponse;

class OTPClient
{
    const DEFAULT_TTL = 300;

    /**
     * @var OAuthClient
     */
    private $oauthClient;

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
        $this->oauthClient = new OAuthClient(
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
            'Authorization' => 'Bearer ' . $this->oauthClient->getAccessToken(),
        ])
            ->withoutVerifying();

        $response = $handler($request);

        if ($response->status() == 401) {
            $this->oauthClient->getAccessToken(true);
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
     * @param string $verifiable
     * @param string $code
     * @return bool
     */
    public function check($verifiable, $code)
    {
        $params = [
            'verifiable' => $verifiable,
            'otp_code' => $code,
        ];

        return $this->request(function (PendingZttpRequest $request) use ($params) {
            return $request->asJson()
                ->post($this->getUrl('/otp/check'), $params);
        })
            ->isSuccess();
    }

    /**
     * @param string $verifiable
     * @return bool
     */
    public function delete($verifiable)
    {
        return $this->request(function (PendingZttpRequest $request) use ($verifiable) {
            return $request->asJson()
                ->delete($this->getUrl('/otp/' . $verifiable));
        })
            ->isSuccess();
    }
}