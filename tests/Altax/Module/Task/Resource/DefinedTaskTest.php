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

    }
}