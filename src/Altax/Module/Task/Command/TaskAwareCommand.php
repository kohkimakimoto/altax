<?php
namespace Altax\Module\Task\Command;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Altax\Module\Task\Resource\RuntimeTask;

class TaskAwareCommand extends \Altax\Command\Command
{
    protected $task;
    
    public function __construct($task)
    {
        $this->task = $task;
        if ($this->task->hasDescription()) {
            $this->setDescription($this->task->getDescription());
        }

        parent::__construct($task->getName());
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->preProcessForTask($input, $output);

        return parent::run($input, $output);
    }

    protected function executeTaskClosure(InputInterface $input, OutputInterface $output)
    {
        $runtimeTask = new RuntimeTask($this->task, $input, $output);        
        $output->writeln("<info>Running task </info>".$this->task->getName());
        return call_user_func($this->task->getClosure(), $runtimeTask);
    }

    protected function preProcessForTask(InputInterface $input, OutputInterface $output)
    {
        if ($this->task->hasClosure()) {
            $this->setCode(array($this, "executeTaskClosure"));
        }
    }
}