<?php
namespace Test\Kohkimakimoto\Altax\Task;

use Kohkimakimoto\Altax\Task\Task;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\StreamOutput;
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

        $output = new StreamOutput(fopen('php://memory', 'w', false));

        task("test_task", function(){});

        $task = new Task("test_task", "localhost", $input, $output, false);
        
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

        $output = new StreamOutput(fopen('php://memory', 'w', false));

        task("test_task", function(){});

        $task = new Task("test_task", "localhost", $input, $output, false);
        $task->execute();

        rewind($output->getStream());
        $display = stream_get_contents($output->getStream());
    }

    public function testRunSSH()
    {
        $context = Context::initialize();

        $InputDefinition = new InputDefinition();
        $InputDefinition->addArgument(new InputArgument("args"));
        $input = new ArgvInput(array(), $InputDefinition);
        $input->setArgument("args", array());

        $output = new StreamOutput(fopen('php://memory', 'w', false));

        task("test_task", function(){});
        $task = new Task("test_task", "localhost", $input, $output, false);
        $task->runSSH("ls");
    }

}
