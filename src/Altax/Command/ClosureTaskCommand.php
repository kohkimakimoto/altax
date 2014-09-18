<?php
namespace Altax\Command;

use Symfony\Component\Console\Input\InputArgument;

/**
 * Altax closure task command.
 */
class ClosureTaskCommand extends Command
{

    public function __construct($task)
    {
        if (!$task->hasClosure()) {
            throw new \RuntimeException("The task don't have a closure");
        }
        parent::__construct($task);
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
        return call_user_func($this->task->getClosure(), $task);
    }
}
