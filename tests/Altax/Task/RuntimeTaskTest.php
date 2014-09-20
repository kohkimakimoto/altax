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
}


