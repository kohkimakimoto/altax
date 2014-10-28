<?php
namespace Altax\Facade;

class LocalFilesystem extends \Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'local_filesystem'; }
}
