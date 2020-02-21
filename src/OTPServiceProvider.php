<?php

namespace SdkLaravel;

use Illuminate\Support\ServiceProvider;

class OTPServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom($this->configPath(), 'cors');

        $this->app->singleton('otp.client', function ($app) {
            $options = $app['config']->get('OTP');

            if (isset($options['access_token'])) {
                return new OTPClient($options['access_token']);
            }

            return new OTPClient(null);
        });
    }

    protected function configPath()
    {
        return __DIR__ . '/../config/otp.php';
    }
}
