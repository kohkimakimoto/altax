<?php
namespace Altax\Facade;

class Command extends \Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'shell.command'; }
}
