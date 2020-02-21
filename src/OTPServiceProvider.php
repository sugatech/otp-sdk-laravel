<?php

namespace OTP\SdkLaravel;

use Illuminate\Support\ServiceProvider;
use OTPClient;

class OTPServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('otp.client', function () {
            return new OTPClient();
        });
    }
}
