<?php
namespace Altax\Facade;

class Filesystem extends \Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'filesystem'; }
}
