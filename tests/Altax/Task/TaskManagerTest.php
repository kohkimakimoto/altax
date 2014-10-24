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

    public function testCall()
    {
        $application = new Application($this->app);
        $application->setAutoExit(false);

        $taskManager = $this->app["task"];
        $task = $taskManager->register("test", function(){

            $this->app["task"]->call("test2");

        });
        $task2 = $taskManager->register("test2", function(){

            $this->app["task"]->call("test3");
            $this->app["task"]->call("test3", ["v" => "hoge"]);
            $this->app["task"]->call("test3", ["foo"]);

        });
        $task3 = $taskManager->register("test3", function($v = "aaa", $v2 = "bbb"){

            $this->app["output"]->write($v);

        });

        $application->add($task->makeCommand());
        $application->add($task2->makeCommand());
        $application->add($task3->makeCommand());

        $command = $application->find("test");
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                "command" => $command->getName(),
                )
            );
        $this->assertRegExp("/aaahogefoo/", $this->app['output']->fetch());

    }

}

class Test01Command extends \Altax\Command\Command
{

}
