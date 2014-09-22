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
        $this->app->bind('process', function ($app) {
            return new Executor($app['servers'], $app['output'], $app['command']);
        });
    }
}
