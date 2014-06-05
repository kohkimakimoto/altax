<?php
namespace Test\Altax\Module\Task\Facade;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;

use Altax\Foundation\Container;
use Altax\Foundation\ModuleFacade;
use Altax\Module\Task\Facade\Task;

class TaskTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->container = new Container();

        ModuleFacade::clearResolvedInstances();
        ModuleFacade::setContainer($this->container);

        $module = new \Altax\Module\Task\TaskModule($this->container);

        $this->container->addModule(Task::getModuleName(), $module);
    }

    public function testDefault()
    {
    }
}
