<?php

namespace SdkLaravel;

use Illuminate\Support\ServiceProvider;

class OTPServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom($this->configPath(), 'access_token');

        $this->app->singleton('otp.client', function ($app) {
            $options = $app['config']->get('OTP');

            if (isset($options['access_token']) && isset($options['api_url'])) {
                return new OTPClient($options['api_url'], $options['access_token']);
            }

            return new OTPClient(null,null);
        });
    }

    protected function configPath()
    {
        return __DIR__ . '/../config/otp.php';
    }
}
