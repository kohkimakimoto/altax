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
        $this->app->bindShared('process.runtime', function ($app) {
            return new Runtime();
        });

        $this->app->bindShared('process.executor', function ($app) {
            return new Executor(
                $app['process.runtime'],
                $app['servers'],
                $app['output'],
                $app['console']);
        });
    }
}
