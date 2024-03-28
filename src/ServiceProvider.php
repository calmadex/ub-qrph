<?php

namespace Ezi\UbQrPh;

use Ezi\UbQrPh\Contracts\QrPhInterface;
use Ezi\UbQrPh\Facade\QrPh;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/ub-qrph.php' => config_path('ub-qrph.php'),
        ], 'ub-qrph');

        $this->app->bind('ub-qrph', function($app) {
            $config = $app['config']['ub-qrph'];
            return new QrPh($config);
        });

        $this->app->bind(QrPhInterface::class, function ($app) {
            $config = $app['config']['ub-qrph'];
            return new QrPhClient($config);
        });
    }
}
