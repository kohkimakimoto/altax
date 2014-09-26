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
        $this->app->bindShared('process.executor', function ($app) {
            return new Executor($app, $app['servers'], $app['output'], $app['console']);
        });

        $this->app->bindShared('process.main_process', function ($app) {
            return Process::createMainProcess();
        });

        $this->app->instance('process.current_process', $this->app['process.main_process']);
    }
}
