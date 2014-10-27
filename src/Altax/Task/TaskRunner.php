<?php
namespace Altax\Task;

use Symfony\Component\Console\Input\ArrayInput;

/**
 * TaskManager
 */
class TaskRunner
{
    protected $output;

    protected $console;

    public function __construct($output, $console)
    {
        $this->console = $console;
        $this->output = $output;
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

        if (is_vector($inputArguments)) {
            foreach ($commandArguments as $key => $commandArgument) {
                if ($key === "command") {
                    continue;
                }
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
