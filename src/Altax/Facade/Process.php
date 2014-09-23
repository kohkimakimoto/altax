<?php
namespace Altax\Facade;

class Process extends \Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        // Do not resolove instance.
        return static::$app['process.executor'];
    }
}
