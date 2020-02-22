<?php

namespace OTP\SDK;

use GuzzleHttp\Client;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;

class OTPServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom($this->configPath(), 'otp');

        $this->app->singleton('otp.client', function ($app) {
            $options = $app['config']->get('otp');

            if (!isset($options['api_url'])) {
                throw new \InvalidArgumentException('Not found api_urL config');
            }

            if (!isset($options['access_token'])) {
                throw new \InvalidArgumentException('Not found access_token config');
            }

            return new OTPClient(app(Client::class), $options['api_url'], $options['access_token']);
        });
    }

    public function boot()
    {
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$this->configPath() => config_path('otp.php')], 'otp');
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('otp');
        }
    }

    protected function configPath()
    {
        return __DIR__ . '/../config/otp.php';
    }

}
