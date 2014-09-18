<?php
namespace Altax\Server;

use Illuminate\Support\ServiceProvider;

class ServerServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('server', function ($app) {
            return new Server($app['events']);
        });
    }
}
