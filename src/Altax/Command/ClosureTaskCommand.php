<?php
namespace Altax\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ClosureTaskCommand extends \Altax\Command\Command
{
    public function __construct($definedTask)
    {
        if (!$definedTask->hasClosure()) {
            throw new \RuntimeException("The task don't have a closure");
        }
        parent::__construct($definedTask);
    }

    protected function fire($task)
    {
        return call_user_func($this->definedTask->getClosure(), $task);
    }
}