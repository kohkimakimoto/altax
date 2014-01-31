<?php

namespace Altax\Module\Task;

use Altax\Foundation\Module;
use Altax\Module\Task\Task;

class TaskModule extends Module
{
    public function register()
    {
        $args = func_get_args();

        if (count($args) < 2) {
            throw new \RuntimeException("Missing argument. Must 2 arguments at minimum.");
        }

        $task = new Task();
        $task->name = $args[0];
        if ($args[1] instanceof \Closure) {
            $task->closure = $args[1];
        } elseif (is_string($args[1])) {
            $task->command = $args[1];
        }

        $this->container->set("tasks/".$task->name, $task);

        return $task;
    }
}