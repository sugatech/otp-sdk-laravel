<?php

namespace OTP\SDK;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class OTPServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom($this->configPath(), 'api_url');
        $this->mergeConfigFrom($this->configPath(), 'access_token');

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
        if (!$this->isLumen()) {
            $this->publishes([$this->configPath() => config_path('otp.php')]);
        }
    }

    protected function configPath()
    {
        return __DIR__ . '/../config/otp.php';
    }

    protected function isLumen()
    {
        return Str::contains($this->app->version(), 'Lumen');
    }
}
