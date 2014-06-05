<?php
namespace Altax\Module\Task\Resource;

use Symfony\Component\Console\Input\ArrayInput;
use Altax\Module\Task\Process\Executor;

/**
 * Runtime task
 */
class RuntimeTask
{
    protected $task;
    protected $input;
    protected $output;

    public function __construct($command, $task, $input, $output)
    {
        $this->command = $command;
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

    public function getCommand()
    {
        return $this->command;
    }

    public function writeln($string)
    {
        $this->output->writeln($string);
    }

    public function write($string)
    {
        $this->output->write($string);
    }

    public function getArguments()
    {
        if ($this->input->hasArgument('args') 
            && $args = $this->input->getArgument('args')) {
            return $args;
        } else {
            return null;
        }
    }

    public function getArgument($index = 0, $default = null)
    {
        $retVal = null;
        if ($args = $this->getArguments()) {
            if (isset($args[$index])) {
                $retVal = $args[$index];
            } else {
                $retVal = $default;
            }
        } else {
            $retVal = $default;
        }
        return $retVal;
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
