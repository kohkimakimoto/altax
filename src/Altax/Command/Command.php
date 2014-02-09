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

    protected function getContainer()
    {
        return $this->getApplication()->getContainer();
    }
    
    public function __construct($definedTask)
    {
        $this->definedTask = $definedTask;
        $this->setName($this->definedTask->getName());

        if ($this->definedTask->hasDescription()) {
            $this->setDescription($this->definedTask->getDescription());
        }

        parent::__construct();

        // Override the command name.
        $this->setName($this->definedTask->getName());
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $runtimeTask = new RuntimeTask($this->definedTask, $input, $output);        
        
        if ($output->isVerbose()) {
            $output->writeln("<info>Starting task: </info>".$this->definedTask->getName());
        }
        
        $this->runBeforeTask($output);

        if ($output->isVerbose()) {
            $output->writeln("<info>Running task: </info>".$this->definedTask->getName());
        }
        
        $retVal = $this->fire($runtimeTask);

        $this->runAfterTask($output);


        if ($output->isVerbose()) {
            $output->writeln("<info>Finished task: </info>".$this->definedTask->getName());
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

            $command = $this->getApplication()
                ->find($task->getName())
                ;

            if (!$command) {
                throw new \RuntimeException("Not found a before task command '$taskName'.");
            }

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

            $command = $this->getApplication()
                ->find($task->getName())
                ;

            if (!$command) {
                throw new \RuntimeException("Not found a after task command '$taskName'.");
            }

            $input = new ArrayInput(array("command" => $task->getName()));
            $command->run($input, $output);
        }
    }
}