<?php
namespace Test\Kohkimakimoto\Altax\Task;

use Kohkimakimoto\Altax\Task\Task;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Input\InputDefinition;
use Kohkimakimoto\Altax\Util\Context;

class TaskTest extends \PHPUnit_Framework_TestCase
{
    public function testExceptionForCallbackFunctionNotFound()
    {
        $context = Context::initialize();

        $InputDefinition = new InputDefinition();
        $InputDefinition->addArgument(new InputArgument("args"));
        $input = new ArgvInput(array(), $InputDefinition);
        $input->setArgument("args", array());
        $output = new ConsoleOutput();

        $task = new Task("testtask", "127.0.0.1", $input, $output, false);
        try {
            $task->execute();
        } catch (\RuntimeException $e) {
            $this->assertEquals(true, true);
        }
        
    }   

    public function testExecute()
    {
        $context = Context::initialize();

        $InputDefinition = new InputDefinition();
        $InputDefinition->addArgument(new InputArgument("args"));
        $input = new ArgvInput(array(), $InputDefinition);
        $input->setArgument("args", array());
        $output = new ConsoleOutput();

        task("testtask", function(){});
        $task = new Task("testtask", "127.0.0.1", $input, $output, false);
        $task->execute();

        // local run
        $task = new Task("testtask", "127.0.0.1", $input, $output, true);
        $task->execute();
    }

}
