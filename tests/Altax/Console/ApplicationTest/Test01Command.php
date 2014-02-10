<?php
namespace Test\Altax\Console\ApplicationTest;

class Test01Command extends \Altax\Command\Command
{
    protected function fire($task)
    {
        $task->writeln("Fired test01 command task!");
    }
}
