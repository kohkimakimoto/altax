<?php
namespace Test\Altax\Module\Task\Facade;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;

use Altax\Foundation\Container;
use Altax\Foundation\ModuleFacade;

class TaskTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->container = new Container();

        ModuleFacade::clearResolvedInstances();
        ModuleFacade::setContainer($this->container);
    }

    public function testDefult()
    {

    }
}