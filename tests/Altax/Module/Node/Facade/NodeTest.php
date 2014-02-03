<?php
namespace Test\Altax\Module\Node;

use Altax\Foundation\Container;
use Altax\Foundation\ModuleFacade;
use Altax\Module\Node\Facade\Node;

class NodeTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->container = new Container();

        ModuleFacade::clearResolvedInstances();
        ModuleFacade::setContainer($this->container);

        $module = new \Altax\Module\Node\NodeModule();
        $module->setContainer($this->container);

        $this->container->addModule(Node::getModuleName(), $module);
        
    }

    public function testSet()
    {
        try {
            Node::host();
            $this->assertEquals(false, true);
        } catch (\RuntimeException $e) {
            $this->assertEquals(true, true);
        }

        Node::host("web1.exsample.com");
        Node::host("web2.exsample.com");

        //Facade::node("web2.exsample.com");

        // print_r($this->container->get("nodes"));

    }

}