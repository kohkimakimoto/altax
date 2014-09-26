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
        $this->app->bindShared('shell.command', function ($app) {
            return new CommandBuilder(
                $app,
                $app['output'],
                $app['env']);
        });
        $this->app->bindShared('shell.script', function ($app) {
            return new ScriptBuilder(
                $app,
                $app['output'],
                $app['env']);
        });
    }
}
