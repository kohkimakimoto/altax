<?php
namespace Altax\Task;

use Illuminate\Support\ServiceProvider;

class TaskServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('task', function ($app) {
            return new TaskBuilder();
        });
    }
}
