<?php
namespace Altax\Task;

/**
 * TaskManager
 */
class TaskManager
{
    protected $tasks = array();

    public function register()
    {
        $args = func_get_args();

        if (count($args) < 2) {
            throw new \InvalidArgumentException("Missing argument. Must 2 arguments at minimum.");
        }

        $task = new Task($args[0], $this);

        if ($args[1] instanceof \Closure) {
            // Task is a closure
            $task->setClosure($args[1]);
        } elseif (is_string($args[1])) {
            // Task is a command class.
            $task->setCommandClass($args[1]);
        }

        $this->tasks[$task->getName()] = $task;

        return $task;
    }

    public function getTasks()
    {
        return $this->tasks;
    }

    public function getTask($name, $default = null)
    {
        return isset($this->tasks[$name]) ? $this->tasks[$name] : $default;
    }

    public function get($name)
    {
        return $this->getTask($name);
    }
}
