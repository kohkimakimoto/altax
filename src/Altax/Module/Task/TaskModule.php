<?php

namespace Altax\Module\Task;

use Altax\Foundation\Module;
use Altax\Module\Task\Resource\Task;

class TaskModule extends Module
{
    public function register()
    {
        $args = func_get_args();

        if (count($args) < 2) {
            throw new \RuntimeException("Missing argument. Must 2 arguments at minimum.");
        }

        $task = new Task();
        $task->setContainer($this->getContainer());
        $task->setName($args[0]);

        if ($args[1] instanceof \Closure) {
            // Task is a closure
            $task->setClosure($args[1]);
        } elseif (is_string($args[1])) {
            // Task is a command class.
            $r = new \ReflectionClass($args[1]);
            $command = $r->newInstance($task->getName());
            $command->setApplication($this->container->getApp());
            $task->setCommand($command);
        }


        $this->container->set("tasks/".$task->getName(), $task);

        return $task;
    }

}