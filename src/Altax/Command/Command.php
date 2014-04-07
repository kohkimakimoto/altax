<?php
namespace Altax\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Altax\Module\Task\Resource\RuntimeTask;

/**
 * Altax base command class for defining task.
 */
abstract class Command extends \Symfony\Component\Console\Command\Command
{
    protected $definedTask;
    protected $ancestry = array();

    protected function getContainer()
    {
        return $this->getApplication()->getContainer();
    }

    public function __construct($definedTask)
    {
        $this->definedTask = $definedTask;
        $this->setName($this->definedTask->getName());

        parent::__construct();

        // Override the command name.
        $this->setName($this->definedTask->getName());
        // Override the command description.
        if ($this->definedTask->hasDescription()) {
            $this->setDescription($this->definedTask->getDescription());
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $runtimeTask = new RuntimeTask($this, $this->definedTask, $input, $output);

        if ($output->isVerbose()) {
            $output->writeln("<info>Starting </info>".$this->definedTask->getName());
        }

        $this->ancestry[] = $this->definedTask->getName();

        if ($output->isDebug()) {
            $output->writeln("<info>Current ancestry is </info>".implode(" > ", $this->ancestry));
        }

        $this->runBeforeTask($output);

        if ($output->isVerbose()) {
            $output->writeln("<info>Running </info>".$this->definedTask->getName());
        }

        $retVal = $this->fire($runtimeTask);

        $this->runAfterTask($output);


        if ($output->isVerbose()) {
            $output->writeln("<info>Finished </info>".$this->definedTask->getName());
        }

        return $retVal;
    }

    protected function fire($task)
    {
        throw new \RuntimeException("You need to override 'fire' method.");
    }

    public function getDefinedTask()
    {
        return $this->definedTask;
    }

    public function getTaskConfig()
    {
        return $this->definedTask->getConfig();
    }

    protected function runBeforeTask($output)
    {
        $tasks = $this->definedTask->getBeforeTasks();
        foreach ($tasks as $task) {

            if ($output->isVerbose()) {
                $output->writeln("<info>Found a before task need to run: </info>".$task->getName());
            }

            if (in_array($task->getName(), $this->ancestry)) {
                $output->writeln("<error>Skip a before task ".$task->getName()." to prevent infinite loop. Because of existing it in ancestry tasks.</error>");
                continue;
            }

            $command = $this->getApplication()
                ->find($task->getName())
                ;

            if (!$command) {
                throw new \RuntimeException("Not found a before task command '$taskName'.");
            }

            $command->setAncestry($this->ancestry);

            $input = new ArrayInput(array("command" => $task->getName()));
            $command->run($input, $output);
        }
    }

    protected function runAfterTask($output)
    {
        $tasks = $this->definedTask->getAfterTasks();
        foreach ($tasks as $task) {

            if ($output->isVerbose()) {
                $output->writeln("<info>Found a after task need to run: </info>".$task->getName());
            }

            if (in_array($task->getName(), $this->ancestry)) {
                $output->writeln("<error>Skip a before task ".$task->getName()." to prevent infinit loop. Because of existing it in ancestry tasks.</error>");
                continue;
            }

            $command = $this->getApplication()
                ->find($task->getName())
                ;

            if (!$command) {
                throw new \RuntimeException("Not found a after task command '$taskName'.");
            }

            $command->setAncestry($this->ancestry);

            $input = new ArrayInput(array("command" => $task->getName()));
            $command->run($input, $output);
        }
    }

    public function setAncestry($ancestry)
    {
        $this->ancestry = $ancestry;
    }
}
