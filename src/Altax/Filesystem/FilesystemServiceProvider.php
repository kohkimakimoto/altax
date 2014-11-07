<?php
namespace Altax\Filesystem;

use Illuminate\Support\ServiceProvider;

class FilesystemServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('filesystem', function ($app) {
            return new FilesystemBuilder(
                $app['shell.command'],
                $app['process.runtime'],
                $app['output']
                );
        });

        $this->app->bindShared('local_filesystem', function ($app) {
            return new LocalFilesystemBuilder(
                $app['shell.command'],
                $app['process.runtime'],
                $app['output']
                );
        });

    }
}
