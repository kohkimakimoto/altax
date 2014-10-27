<?php
namespace Test\Altax\Task;

use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Tester\CommandTester;
use Altax\Console\Application;

class TaskManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
        $this->app->instance("output", new BufferedOutput());
    }

    public function testRegisterClosure()
    {
        $taskManager = $this->app["task"];
        $task = $taskManager->register("test", function(){});
        $this->assertEquals("test", $task->getName());
        $this->assertEquals(true, $task->hasClosure());
        $this->assertEquals($taskManager->getTask("test"), $task);
    }

    public function testRegisterCommand()
    {
        $taskManager = $this->app["task"];
        $task = $taskManager->register("test", "Test\Altax\Task\Test01Command");
        $this->assertEquals("test", $task->getName());
        $this->assertEquals(false,  $task->hasClosure());
        $this->assertEquals($taskManager->getTask("test"), $task);
    }

    public function testGetTasks()
    {
        $taskManager = $this->app["task"];
        $task = $taskManager->register("test", function(){});

        $tasks = $taskManager->getTasks();
        $this->assertEquals(1, count($tasks));
    }
}

class Test01Command extends \Altax\Command\Command
{

}
