<?php
namespace Altax\Facade;

class TaskRunner extends \Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'task_runner'; }
}
