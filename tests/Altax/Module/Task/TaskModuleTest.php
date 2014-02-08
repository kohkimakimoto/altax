<?php
namespace Test\Altax\Module\Task;

use Altax\Foundation\Container;
use Altax\Foundation\ModuleFacade;
use Altax\Module\Task\TaskModule;

class TaskModuleTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->container = new Container();
    }

    public function testRegisterClosure()
    {
        $module = new TaskModule($this->container);

        $task = $module->register("test", function(){});
        $this->assertEquals("test", $task->getName());
        $this->assertEquals(true, $task->hasClosure());
        $this->assertEquals($this->container->get("tasks/test"), $task);
    }

    public function testRegisterCommand()
    {
        $module = new TaskModule($this->container);

        require_once __DIR__."/TaskModuleTest/Test01Command.php";

        $task = $module->register("test", "Test\Altax\Module\Task\TaskModuleTest\Test01Command");
        $this->assertEquals("test", $task->getName());
        $this->assertEquals(false,  $task->hasClosure());
        $this->assertEquals($this->container->get("tasks/test"), $task);
    }

    public function testGetTaks()
    {
        $module = new TaskModule($this->container);

        $task = $module->register("test", function(){});
        $this->assertEquals($task, $module->getTask("test"));
    }

    public function testGet()
    {
        $module = new TaskModule($this->container);

        $task = $module->register("test", function(){});
        $this->assertEquals($task, $module->get("test"));
    }
}