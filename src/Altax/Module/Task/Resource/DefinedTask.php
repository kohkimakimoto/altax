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
    protected $beforeTaskNames = array();
    protected $afterTaskNames = array();
    protected $isHidden = false;

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
        return $this;
    }

    public function getContainer()
    {
        return $this->container;
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

    public function createCommandInstance()
    {   
        $command = null;
        if ($this->hasClosure()) {
            $command = new ClosureTaskCommand($this);
        } elseif ($this->hasCommandClass()) {
            $r = new \ReflectionClass($this->getCommandClass());
            $command = $r->newInstance($this);
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
            } elseif (\Altax\Util\Arr::isVector($arg)) {
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
            } elseif (\Altax\Util\Arr::isVector($arg)) {
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

}