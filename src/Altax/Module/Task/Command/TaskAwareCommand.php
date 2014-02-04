<?php
namespace Altax\Module\Task\Command;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TaskAwareCommand extends \Altax\Command\Command
{
    protected $task;
    
    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->preProcessForTask($input, $output);
        return parent::run($input, $output);
    }

    protected function executeTaskClosure(InputInterface $input, OutputInterface $output)
    {
        return call_user_func($this->task->closure, $this->task);
    }

    public function initializeWithTask($task)
    {
        $this->setTask($task);

        if ($task->hasDescription()) {
            $this->setDescription($task->description);
        }
    }

    protected function preProcessForTask(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Running task </info>".$this->task->name);
        $this->task->setInput($input);
        $this->task->setOutput($output);

        if ($this->task->hasClosure()) {
            $this->setCode(array($this, "executeTaskClosure"));
        }
    }

    public function setTask($task)
    {
        $this->task = $task;
    }
}