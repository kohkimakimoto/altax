<?php
namespace Altax\Module\Task\Resource;

use Altax\Module\Task\Process\Process;

class Task
{

    protected $container;
    protected $name;
    protected $closure;
    protected $command;
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

    public function setCommand($command)
    {
        $this->command = $command;
    }

    public function getCommand()
    {
        return $this->command;
    }

    public function hasCommand()
    {
        return isset($this->command);
    }
}