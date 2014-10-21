<?php
namespace Altax\Env;

use Illuminate\Support\ServiceProvider;

class EnvServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('env', function ($app) {
            return new Env($app['app']);
        });
    }
}
