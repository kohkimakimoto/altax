<?php
namespace Altax\Module\Task\Resource;

use Altax\Module\Task\Process\Process;
use \Altax\Command\ClosureTaskCommand;

class DefinedTask
{
    protected $container;
    protected $name;
    protected $closure;
    protected $commandClass;
    protected $description;

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function setDescription($description)
    {
        $this->description = $description;
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
        $this->closure = $closure;
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
    }

    public function getCommandClass()
    {
        return $this->commandClass;
    }

    public function hasCommandClass()
    {
        return isset($this->commandClass);
    }

    public function createCommandInstance()
    {   
        $command = null;
        if ($this->hasClosure()) {
            $command = new ClosureTaskCommand($this);
        } elseif ($this->hasCommandClass()) {
            $r = new \ReflectionClass($this->getCommandClass());
            $command = $r->newInstance($this->getName());

        } else {
            throw new \RuntimeException("Couldn't create command instance from a task named '".$this->name."'.");
        }

        return $command;
                
    }
}