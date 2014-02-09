<?php
namespace Test\Altax\Module\Task\Resource;

use Altax\Foundation\Container;
use Altax\Foundation\ModuleFacade;
use Altax\Module\Task\TaskModule;
use Altax\Module\Task\Resource\DefinedTask;

class DefinedTaskTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->container = new Container();
    }

    public function testNewInstance()
    {
        $task = DefinedTask::newInstance("test", $this->container);
        $this->assertEquals("test", $task->getName());
        $this->assertEquals($this->container, $task->getContainer());
    }

    public function testBasicAccessor()
    {
        $task = DefinedTask::newInstance("test", $this->container);
        $task->setName("test2");
        $this->assertEquals("test2", $task->getName());

        $container = new Container();
        $task->setContainer($container);
        $this->assertSame($container, $task->getContainer());
        $this->assertNotSame($this->container, $task->getContainer());

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

        $task->setConfig(array("key" => "value"));
        $this->assertSame(array("key" => "value"), $task->getConfig());
    }

    public function testDescription()
    {
        $task = DefinedTask::newInstance("test", $this->container);
        $task->description("aaaaaaa");

        $this->assertEquals("aaaaaaa", $task->getDescription());
    }

    public function testConfig()
    {
        $task = DefinedTask::newInstance("test", $this->container);
        $task->config(array("a" => "b"));

        $this->assertSame(array("a" => "b"), $task->getConfig());
    }
}