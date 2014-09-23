<?php
namespace Altax\Shell;

use Illuminate\Support\ServiceProvider;

class ShellServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('shell.command', function ($app) {
            return new CommandBuilder($app['process.current_process'], $app['output']);
        });
    }
}
