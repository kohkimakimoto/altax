<?php
namespace Altax\Task;

use Symfony\Component\Console\Input\ArrayInput;

/**
 * TaskManager
 */
class TaskManager
{
    protected $tasks = array();

    protected $servers;

    protected $input;

    protected $output;

    protected $console;

    public function __construct($servers, $input, $output, $console)
    {
        $this->servers = $servers;
        $this->console = $console;
        $this->input = $input;
        $this->output = $output;
    }

    public function register()
    {
        $args = func_get_args();

        if (count($args) < 2) {
            throw new \InvalidArgumentException("Missing argument. Must 2 arguments at minimum.");
        }

        $task = new Task($args[0], $this, $this->servers);

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

    public function call($name, $inputArguments = array())
    {
        $arguments = array();
        if ($this->output->isDebug()) {
            $this->output->writeln("Calling task: ".$name);
        }

        $command = $this->console->get($name);

        $definition = $command->getDefinition();
        $commandArguments = $definition->getArguments();
        // The fist argument is always 'command' that is the command name.
        array_shift($commandArguments);

        if (is_vector($inputArguments)) {
            foreach ($commandArguments as $key => $commandArgument) {
                $arguments[$key] = array_shift($inputArguments);
            }
        } else {
            $arguments = $inputArguments;
        }

        $arguments['command'] = $name;
        $input = new ArrayInput($arguments);

        return $command->run($input, $this->output);
    }
}
