<?php
namespace Test\Altax\Foundation;

use Altax\Foundation\Container;
use Altax\Foundation\ModuleFacade;

class ModuleFacadeTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->container = new Container();
    }

    public function testDefault()
    {
        ModuleFacade::clearResolvedInstances();
        ModuleFacade::setContainer($this->container);
        try {
            ModuleFacade::getModuleName();
            $this->assertEquals(false, true);
        } catch(\RuntimeException $e) {
            $this->assertEquals(true, true);
        }
        ModuleFacade::getContainer();
        ModuleFacade::clearResolvedInstance("Task");
    }
}