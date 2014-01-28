<?php
namespace Test\Altax\Module\Node;

use Altax\Foundation\Container;
use Altax\Foundation\ModuleFacade;
use Altax\Module\Node\Facade;

class FacadeTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->container = new Container();

        ModuleFacade::clearResolvedInstances();
        ModuleFacade::setContainer($this->container);

        $module = new \Altax\Module\Node\NodeModule();
        $module->setContainer($this->container);
        $this->container->addModule(Facade::getModuleName(), $module);
        
    }

    public function testSet()
    {
        try {
            Facade::host();
            $this->assertEquals(false, true);
        } catch (\RuntimeException $e) {
            $this->assertEquals(true, true);
        }

        Facade::host("web1.exsample.com");
        Facade::host("web2.exsample.com");

        //Facade::node("web2.exsample.com");

        print_r($this->container->get("nodes"));

    }

}