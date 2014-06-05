<?php
namespace Altax\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Altax closure task command.
 */
class ClosureTaskCommand extends \Altax\Command\Command
{

    public function __construct($definedTask)
    {
        if (!$definedTask->hasClosure()) {
            throw new \RuntimeException("The task don't have a closure");
        }
        parent::__construct($definedTask);
    }

    protected function configure()
    {
        $this
            ->addArgument(
                'args',
                InputArgument::IS_ARRAY,
                'Arguments passed to the task.'
            )
            ;        
    }

    protected function fire($task)
    {
        return call_user_func($this->definedTask->getClosure(), $task);
    }
}
