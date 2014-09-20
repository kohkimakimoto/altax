<?php
namespace Altax\Task;

use Altax\Command\ClosureTaskCommand;

/**
 * Defined Task
 */
class Task
{
    protected $name;

    protected $taskManager;

    protected $server;

    protected $closure;

    protected $commandClass;

    protected $description;

    protected $beforeTaskNames = array();

    protected $afterTaskNames = array();

    protected $isHidden = false;

    protected $config = array();

    public function __construct($name, $taskManager, $server)
    {
        $this->name = $name;
        $this->taskManager = $taskManager;
        $this->server = $server;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getServer()
    {
        return $this->server;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function hasDescription()
    {
        return isset($this->description);
    }

    public function setClosure($closure)
    {
        if (!$closure instanceof \Closure) {
            throw new \RuntimeException("Passed not a closure");
        }

        $this->closure = $closure;

        return $this;
    }

    public function getClosure()
    {
        return $this->closure;
    }

    public function hasClosure()
    {
        return isset($this->closure);
    }

    public function setCommandClass($commandClass)
    {
        $this->commandClass = $commandClass;

        return $this;
    }

    public function getCommandClass()
    {
        return $this->commandClass;
    }

    public function hasCommandClass()
    {
        return isset($this->commandClass);
    }

    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getBeforeTasks()
    {
        $tasks = array();
        foreach ($this->beforeTaskNames as $taskName) {
            $task = $this->taskManager->getTask($taskName);
            if (!$task) {
                throw new \RuntimeException("Registered before task '$taskName' is not found.");
            }
            $tasks[] = $task;
        }

        return $tasks;
    }

    public function getAfterTasks()
    {
        $tasks = array();
        foreach ($this->afterTaskNames as $taskName) {
            $task = $this->taskManager->getTask($taskName);
            if (!$task) {
                throw new \RuntimeException("Registered after task '$taskName' is not found.");
            }
            $tasks[] = $task;
        }

        return $tasks;
    }

    public function makeCommand()
    {
        $command = null;
        if ($this->hasClosure()) {
            $command = new ClosureTaskCommand($this);
        } elseif ($this->hasCommandClass()) {
            try {
                $r = new \ReflectionClass($this->getCommandClass());
                $command = $r->newInstance($this);
            } catch (\ReflectionException $e) {
                // The task class is not defined.
                // Replace closure task with alert description.
                $class = $this->getCommandClass();
                $this->setClosure(function ($task) use ($class) {
                    $task->writeln("<error>This task references unresolved class '$class'</error>");
                });
                $this->setDescription("<error>This task references unresolved class '$class'</error>".$this->getDescription());
                $command = new ClosureTaskCommand($this);
            }

        } else {
            throw new \RuntimeException("Couldn't create command instance from a task named '".$this->name."'.");
        }

        return $command;
    }

    public function description($description)
    {
        return $this->setDescription($description);
    }

    public function before()
    {
        $args = func_get_args();

        foreach ($args as $arg) {
            if (is_string($arg)) {
                $this->beforeTaskNames[] = $arg;
            } elseif (is_vector($arg)) {
                $this->beforeTaskNames = array_merge($this->beforeTaskNames, $arg);
            }
        }

        return $this;
    }

    public function after()
    {
        $args = func_get_args();

        foreach ($args as $arg) {
            if (is_string($arg)) {
                $this->afterTaskNames[] = $arg;
            } elseif (is_vector($arg)) {
                $this->afterTaskNames = array_merge($this->afterTaskNames, $arg);
            }
        }

        return $this;
    }

    public function hidden()
    {
        $this->isHidden = true;

        return $this;
    }

    public function isHidden()
    {
        return $this->isHidden;
    }

    public function config($config)
    {
        return $this->setConfig($config);
    }
}
