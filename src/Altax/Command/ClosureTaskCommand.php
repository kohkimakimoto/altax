<?php
namespace Altax\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Altax\Module\Task\Resource\RuntimeTask;

class ClosureTaskCommand extends \Altax\Command\Command
{
    protected $task;
    
    public function __construct($task)
    {
        $this->task = $task;

        if (!$this->task->hasClosure()) {
            throw new \RuntimeException("The task don't have a closure");
        }

        if ($this->task->hasDescription()) {
            $this->setDescription($this->task->getDescription());
        }

        parent::__construct($task->getName());
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $runtimeTask = new RuntimeTask($this->task, $input, $output);        
        $output->writeln("<info>Running task </info>".$this->task->getName());
        return call_user_func($this->task->getClosure(), $runtimeTask);
    }
}