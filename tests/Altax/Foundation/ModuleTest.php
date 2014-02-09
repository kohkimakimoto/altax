<?php
namespace Test\Altax\Foundation;

use Altax\Foundation\Container;
use Altax\Foundation\Module;

class ModuleTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->container = new Container();
    }

    public function testDefault()
    {
        $module = new SampleModule($this->container);
        $module->setContainer(new Container());
        $this->assertNotSame($this->container, $module->getContainer());
    }
}

class SampleModule extends Module
{
}