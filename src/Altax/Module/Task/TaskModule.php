<?php
namespace Altax\Module\Task;

use Altax\Foundation\Module;
use Altax\Module\Task\Resource\DefinedTask;

/**
 * Task module
 */
class TaskModule extends Module
{
    public function register()
    {
        $args = func_get_args();

        if (count($args) < 2) {
            throw new \RuntimeException("Missing argument. Must 2 arguments at minimum.");
        }

        $task = DefinedTask::newInstance($args[0], $this->getContainer());

        if ($args[1] instanceof \Closure) {
            // Task is a closure
            $task->setClosure($args[1]);
        } elseif (is_string($args[1])) {
            // Task is a command class.
            $task->setCommandClass($args[1]);
        }

        $this->container->set("tasks/".$task->getName(), $task);

        return $task;
    }

    public function getTask($name)
    {
        return $this->container->get("tasks/".$name, null);
    }

    public function get($name)
    {
        return $this->getTask($name);
    }
}
