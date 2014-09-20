<?php
namespace Altax\Process;

use Illuminate\Support\ServiceProvider;

class ProcessServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('process', function ($app) {
            return new Process($app['servers']);
        });
    }
}
