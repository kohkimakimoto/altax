<?php
namespace Altax\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Altax\Module\Task\Resource\RuntimeTask;

class Command extends \Symfony\Component\Console\Command\Command
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
            $output->writeln("<info>Running task: </info>".$this->definedTask->getName());
        }
        
        $retVal = $this->fire($runtimeTask);

        if ($output->isVerbose()) {
            $output->writeln("<info>Finished task: </info>".$this->definedTask->getName());
        }

        return $retVal;
    }

    protected function fire($task)
    {
        throw new \RuntimeException("You need to override 'fire' method.");
    }
}