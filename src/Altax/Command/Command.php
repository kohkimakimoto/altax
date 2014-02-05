<?php
namespace Altax\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends \Symfony\Component\Console\Command\Command
{
    protected function getContainer()
    {
        return $this->getApplication()->getContainer();
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $runtimeTask = new RuntimeTask($this->task, $input, $output);        
        $output->writeln("<info>Running task </info>".$this->task->getName());
        
        return $this->fire($runtimeTask);
    }

    protected function fire($task)
    {
        throw new \RuntimeException("You need to override 'fire' method.");
    }
}