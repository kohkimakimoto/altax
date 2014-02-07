<?php
namespace Altax\Module\Task\Resource;

use Symfony\Component\Console\Input\ArrayInput;
use Altax\Module\Task\Process\Process;

class RuntimeTask
{
    protected $task;
    protected $input;
    protected $output;

    public function __construct($task, $input, $output)
    {
        $this->task = $task;
        $this->input = $input;
        $this->output = $output;
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

    public function getConfig()
    {
        return $this->task->getConfig();
    }

    public function writeln($string)
    {
        $this->output->writeln($string);
    }

    public function write($string)
    {
        $this->output->write($string);
    }

    public function process()
    {
        $args = func_get_args();
        
        $process = new Process($this);

        if (count($args) == 1 && is_string($args[0])) {
            // Passed a commandline string to run.
            $process->setCommandline($args[0]);
        } elseif (count($args) == 1 && $args[0] instanceof \Closure) {
            // Passed a closure.
            $process->setClosure($args[0]);
        } else {
            throw new \RuntimeException("Unsupported calling the method.");
        }

        return $process;
    }

    public function run($commandline)
    {
        $this->process($commandline)->run();
    }

    public function call($taskName, $arguments = array())
    {
        if ($this->output->isVerbose()) {
            $this->output->writeln("<info>Calling task: </info><comment>".$taskName."</comment> from ".$this->task->getName());
        }

        $command = $this
            ->task
            ->getContainer()
            ->getApp()
            ->find($taskName)
            ;

        $input = new ArrayInput($arguments);
        return $command->run($this->input, $this->output);
    }

}