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
        $InputDefinition->addArgument(new InputArgument("command"));
        $input = new ArgvInput(array(), $InputDefinition);
        $input->setArgument("args", array());
        $input->setArgument("command", "test_task2");

        $output = new StreamOutput(fopen('php://memory', 'w', false));

        role('localhost', '127.0.0.1');

        task('test_task1', array('roles' => 'localhost'), function($host, $args){

            run("echo Hello");

        });

        task('test_task2', array('roles' => 'localhost'), function($host, $args){

            run_task("test_task1");
        });


        $task = new Task("test_task2", "localhost", $input, $output, false);
        $context->set('currentTask', $task);
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

    public function testRunLocalCommand()
    {
        $context = Context::initialize();

        $InputDefinition = new InputDefinition();
        $InputDefinition->addArgument(new InputArgument("args"));
        $input = new ArgvInput(array(), $InputDefinition);
        $input->setArgument("args", array());

        $output = new StreamOutput(fopen('php://memory', 'w', false));

        task("test_task", function(){});
        $task = new Task("test_task", "localhost", $input, $output, false);
        $task->runLocalCommand("ls");
    }

    public function testGetOutput()
    {
        $context = Context::initialize();

        $InputDefinition = new InputDefinition();
        $InputDefinition->addArgument(new InputArgument("args"));
        $input = new ArgvInput(array(), $InputDefinition);
        $input->setArgument("args", array());

        $output = new StreamOutput(fopen('php://memory', 'w', false));

        task("test_task", function(){});
        $task = new Task("test_task", "localhost", $input, $output, false);
        $v = $task->getOutput();

    }
}
