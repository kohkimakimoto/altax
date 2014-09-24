<?php
namespace Altax\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

/**
 * Altax base command class for defining task.
 */
abstract class Command extends SymfonyCommand
{
    protected $task;

    protected $ancestry = array();

    protected function getContainer()
    {
        return $this->getApplication()->getContainer();
    }

    public function __construct($task)
    {
        $this->task = $task;
        $this->setName($this->task->getName());

        parent::__construct();

        // Override the command name.
        $this->setName($this->task->getName());
        // Override the command description.
        if ($this->task->hasDescription()) {
            $this->setDescription($this->task->getDescription());
        }
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->instance('command', $this);

        return parent::run($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($output->isDebug()) {
            $output->writeln("Starting ".$this->task->getName());
        }

        $this->ancestry[] = $this->task->getName();

        if ($output->isDebug()) {
            $output->writeln("Current ancestry is ".implode(" > ", $this->ancestry));
        }

        $this->runBeforeTask($output);

        if ($output->isDebug()) {
            $output->writeln("Running ".$this->task->getName());
        }

        $retVal = $this->fire();

        $this->runAfterTask($output);

        if ($output->isDebug()) {
            $output->writeln("Finished ".$this->task->getName());
        }

        return $retVal;
    }

    protected function fire()
    {
        throw new \RuntimeException("You need to override 'fire' method.");
    }

    public function getTask()
    {
        return $this->task;
    }

    public function getTaskConfig()
    {
        return $this->task->getConfig();
    }

    protected function runBeforeTask($output)
    {
        $tasks = $this->task->getBeforeTasks();
        foreach ($tasks as $task) {

            if ($output->isDebug()) {
                $output->writeln("Found a before task need to run: ".$task->getName());
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
        $tasks = $this->task->getAfterTasks();
        foreach ($tasks as $task) {

            if ($output->isDebug()) {
                $output->writeln("Found a after task need to run: ".$task->getName());
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
