<?php
namespace Altax\Module\Task\Resource;

use Symfony\Component\Console\Input\ArrayInput;
use Altax\Module\Task\Process\Executor;

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

    public function arguments()
    {
        if ($this->input->hasArgument('args') 
            && $args = $this->input->hasArgument('args')) {
            return $args;
        } else {
            return null;
        }
    }

    public function argument($index = 0)
    {
        if ($args = $this->arguments()) {
            if (isset($args[$index])) {
                return $args[$index];
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function exec($closure, $options = array())
    {
        $executor = new Executor($this, $closure, $options);
        $executor->execute();
    }

    public function call($taskName, $arguments = array())
    {
        if ($this->output->isVerbose()) {
            $this->output->writeln("<info>Calling task: </info>".$taskName." from ".$this->task->getName());
        }

        $command = $this
            ->task
            ->getContainer()
            ->getApp()
            ->find($taskName)
            ;

        if (!$command) {
            throw new \RuntimeException("Not found a before task command '$taskName'.");
        }
        
        $arguments['command'] = $taskName;

        $input = new ArrayInput($arguments);
        return $command->run($this->input, $this->output);
    }

}