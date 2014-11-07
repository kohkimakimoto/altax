<?php
namespace Altax\Facade;

class LocalCommand extends \Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'shell.local_command'; }
}
