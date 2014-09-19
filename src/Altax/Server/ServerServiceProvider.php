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
        $this->app->bindShared('key_passphrase_map', function ($app) {
            return new KeyPassphraseMap();
        });

        $this->app->bindShared('server', function ($app) {
            return new Server($app['key_passphrase_map'], $app['env']);
        });

    }
}
