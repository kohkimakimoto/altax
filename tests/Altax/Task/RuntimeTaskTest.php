<?php
namespace Test\Altax\Task;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Altax\Task\RuntimeTask;

class RuntimeTaskTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
        $this->input = new ArrayInput(array("command" => "test"));
        $this->output = new BufferedOutput();

    }

    public function testConstruct()
    {
        $taskManager = $this->app["task"];
        $task = $taskManager->register("test", function(){});
        $runtimeTask = new RuntimeTask($task->makeCommand(), $task, $this->input, $this->output);

        $this->assertEquals("Altax\Task\RuntimeTask", get_class($runtimeTask));
    }

    public function testSetAndGetInput()
    {
        $taskManager = $this->app["task"];
        $task = $taskManager->register("test", function(){});
        $runtimeTask = new RuntimeTask($task->makeCommand(), $task, $this->input, $this->output);

        $this->assertSame($this->input, $runtimeTask->getInput());
        $input = new ArrayInput(array("command" => "test2"));
        $runtimeTask->setInput($input);
        $this->assertSame($input, $runtimeTask->getInput());
        $this->assertNotSame($this->input, $runtimeTask->getInput());
    }

    public function testSetAndGetOutput()
    {
        $taskManager = $this->app["task"];
        $task = $taskManager->register("test", function(){});
        $runtimeTask = new RuntimeTask($task->makeCommand(), $task, $this->input, $this->output);

        $this->assertSame($this->output, $runtimeTask->getOutput());
        $output = new BufferedOutput();
        $runtimeTask->setOutput($output);
        $this->assertSame($output, $runtimeTask->getOutput());
        $this->assertNotSame($this->output, $runtimeTask->getOutput());
    }

    public function testGetConfig()
    {
        $taskManager = $this->app["task"];
        $task = $taskManager->register("test", function(){});
        $runtimeTask = new RuntimeTask($task->makeCommand(), $task, $this->input, $this->output);

        $this->assertSame($task->getConfig(), $runtimeTask->getConfig());
    }

    public function testWriteln()
    {
        $taskManager = $this->app["task"];
        $task = $taskManager->register("test", function(){});
        $runtimeTask = new RuntimeTask($task->makeCommand(), $task, $this->input, $this->output);

        $runtimeTask->writeln("Write log test");
        $this->assertEquals("Write log test\n", $this->output->fetch());
    }

    public function testWrite()
    {
        $taskManager = $this->app["task"];
        $task = $taskManager->register("test", function(){});
        $runtimeTask = new RuntimeTask($task->makeCommand(), $task, $this->input, $this->output);

        $runtimeTask->write("Write log test");
        $this->assertEquals("Write log test", $this->output->fetch());
    }

    public function testGetArguments()
    {
        $taskManager = $this->app["task"];
        $task = $taskManager->register("test", function(){});
        $runtimeTask = new RuntimeTask($task->makeCommand(), $task, $this->input, $this->output);

        $args = $runtimeTask->getArguments();
    }
}


