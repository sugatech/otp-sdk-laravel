<?php

namespace OTP\SDK;

use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

class OTPServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom($this->configPath(), 'otp');

        $this->app->singleton('otp.client', function ($app) {
            $options = $app['config']->get('otp');

            if (!isset($options['api_url'])) {
                throw new \InvalidArgumentException('Not found api_url config');
            }

            return new OTPClient($options['api_url']);
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
