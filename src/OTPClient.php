<?php

namespace OTP\SDK;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use OAuth2ClientCredentials\OAuthClient;

class OTPClient
{
    public const DEFAULT_TTL = 300;

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
     * @return Response
     * @throws \Illuminate\Http\Client\RequestException
     */
    private function request($handler)
    {
        $request = Http::withHeaders([
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
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function sendSms($phoneNumber, $template, $background = true, $ttl = self::DEFAULT_TTL)
    {
        $params = [
            'phone_number' => $phoneNumber,
            'template' => $template,
            'background' => $background,
            'ttl' => $ttl,
        ];

        return $this->request(function (PendingRequest $request) use ($params) {
            return $request->asJson()
                ->post($this->getUrl('/otp/sms'), $params);
        })
            ->successful();
    }

    /**
     * @param string $mail
     * @param string $template
     * @param bool $background
     * @param int $ttl
     * @return bool
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function sendMail($mail, $template, $background = true, $ttl = self::DEFAULT_TTL)
    {
        $params = [
            'mail' => $mail,
            'template' => $template,
            'background' => $background,
            'ttl' => $ttl,
        ];

        return $this->request(function (PendingRequest $request) use ($params) {
            return $request->asJson()
                ->post($this->getUrl('/otp/mail'), $params);
        })
            ->successful();
    }

    /**
     * @param array $params
     * @return array[]
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function verifications($params = [])
    {
        return $this->request(function (PendingRequest $request) use ($params) {
            return $request->asJson()
                ->get($this->getUrl('/verifications'), $params);
        })
            ->json();
    }

    /**
     * @param string $verifiable
     * @param string $code
     * @return bool
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function check($verifiable, $code)
    {
        $params = [
            'verifiable' => $verifiable,
            'otp_code' => $code,
        ];

        return $this->request(function (PendingRequest $request) use ($params) {
            return $request->asJson()
                ->post($this->getUrl('/otp/check'), $params);
        })
            ->successful();
    }

    /**
     * @param string $phoneNumber
     * @param string|null $locale
     * @param bool $background
     * @param int $ttl
     * @return bool
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function sendVoiceCall($phoneNumber, $locale = null, $background = true, $ttl = self::DEFAULT_TTL)
    {
        $params = [
            'phone_number' => $phoneNumber,
            'locale' => $locale,
            'background' => $background,
            'ttl' => $ttl,
        ];

        return $this->request(function (PendingRequest $request) use ($params) {
            return $request->asJson()
                ->post($this->getUrl('/otp/voice-call'), $params);
        })
            ->successful();
    }

    /**
     * @param string $phoneNumber
     * @param string $templateId
     * @param array $templateData
     * @param string|null $smsMessageTemplate
     * @param string|null $locale
     * @param bool $background
     * @param int $ttl
     * @return bool
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function sendZns($phoneNumber, $templateId, $templateData, $smsTemplate = null, $background = true, $ttl = self::DEFAULT_TTL)
    {
        $params = [
            'phone_number' => $phoneNumber,
            'template_id' => $templateId,
            'template_data' => $templateData,
            'sms_template' => $smsTemplate,
            'background' => $background,
            'ttl' => $ttl,
        ];

        return $this->request(function (PendingRequest $request) use ($params) {
            return $request->asJson()
                ->post($this->getUrl('/otp/zns'), $params);
        })
            ->successful();
    }
}
