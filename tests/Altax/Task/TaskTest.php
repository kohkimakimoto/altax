<?php
namespace Test\Altax\Task;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Altax\Task\Task;

class TaskTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
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
}