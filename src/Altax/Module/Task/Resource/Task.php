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
    protected $input;
    protected $output;

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

    public function getDescription($description)
    {
        $this->description = $description;
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

    public function setInput($input)
    {
        $this->input = $input;
    }

    public function getInput()
    {
        return $this->input;
    }

    public function setOutput($output)
    {
        $this->output = $output;
    }

    public function getOutput()
    {
        return $this->output;
    }

    public function process()
    {
        $args = func_get_args();

        if (count($args) == 1 && is_string($args[0])) {
            $process = new Process($args[0]);
            $process->setTask($this);
            return $process;
        }
    }

    public function run($commandline)
    {
        $this->process($commandline)->run();
    }

}