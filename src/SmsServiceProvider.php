<?php

namespace Fanoosa\Sms;

use Illuminate\Support\ServiceProvider;

class SmsServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('sms', function () {
            return new SmsService();
        });

        $this->mergeConfigFrom(__DIR__ . '/Config/sms.php', 'sms');
    }

    /**
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/Config/sms.php' => config_path('sms.php'),
        ], 'sms');
    }
}
