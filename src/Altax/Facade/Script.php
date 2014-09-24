<?php
namespace Altax\Facade;

class Script extends \Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        // Do not resolove instance.
        return static::$app['shell.script'];
    }
}
