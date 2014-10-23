<?php
namespace Test\Altax\Task;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Tester\CommandTester;
use Altax\Console\Application;
use Altax\Task\Task;

class TaskTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
        $this->app->instance("output", new BufferedOutput());
    }

    public function testBasicAccessor()
    {
        $taskManager = $this->app["task"];
        $task = $taskManager->register("test", function(){});

        $task->setName("test2");
        $this->assertEquals("test2", $task->getName());

        $task->setDescription("dddd");
        $this->assertEquals("dddd", $task->getDescription());
        $this->assertEquals(true, $task->hasDescription());

        $closure = function(){};
        $task->setClosure($closure);
        $this->assertEquals($closure, $task->getClosure());
        $this->assertEquals(true, $task->hasClosure());

        try {
            $task->setClosure("aaaaa");
            $this->assertEquals(true, false);
        } catch (\RuntimeException $e) {
            $this->assertEquals(true, true);
        }

        $task->setCommandClass("FooCommand");
        $this->assertEquals("FooCommand", $task->getCommandClass());
        $this->assertEquals(true, $task->hasCommandClass());
    }

    public function testBefore()
    {
        $taskManager = $this->app["task"];
        $taskBefore = $taskManager->register("test_before", function() {

            $this->app["output"]->write("one");

        });
        $task = $taskManager->register("test", function() {

            $this->app["output"]->write("two");

        })->before('test_before');

        $application = new Application($this->app);
        $application->setAutoExit(false);
        $application->add($task->makeCommand());
        $application->add($taskBefore->makeCommand());
        $command = $application->find("test");

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                "command" => $command->getName(),
                )
            );
        $this->assertRegExp("/onetwo/", $this->app['output']->fetch());
    }

    public function testAfter()
    {
        $taskManager = $this->app["task"];
        $taskAfter = $taskManager->register("test_after", function() {

            $this->app["output"]->write("two");

        });
        $task = $taskManager->register("test", function() {

            $this->app["output"]->write("one");

        })->after('test_after');

        $application = new Application($this->app);
        $application->setAutoExit(false);
        $application->add($task->makeCommand());
        $application->add($taskAfter->makeCommand());
        $command = $application->find("test");

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                "command" => $command->getName(),
                )
            );
        $this->assertRegExp("/onetwo/", $this->app['output']->fetch());
    }
}