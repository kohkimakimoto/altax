<?php
namespace Altax\RemoteFile;

use Illuminate\Support\ServiceProvider;

class RemoteFileServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('remote_file', function ($app) {
            return new RemoteFile(
                $app['process.runtime'],
                $app['output']);
        });
    }
}
