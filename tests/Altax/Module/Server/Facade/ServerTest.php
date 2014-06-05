<?php
namespace Test\Altax\Module\Server\Facade;

use Altax\Foundation\Container;
use Altax\Foundation\ModuleFacade;
use Altax\Module\Server\Facade\Server;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->container = new Container();

        ModuleFacade::clearResolvedInstances();
        ModuleFacade::setContainer($this->container);

        $module = new \Altax\Module\Server\ServerModule($this->container);

        $this->container->addModule(Server::getModuleName(), $module);
    }

    public function testSet()
    {
        try {
            Server::node();
            $this->assertEquals(false, true);
        } catch (\RuntimeException $e) {
            $this->assertEquals(true, true);
        }

        Server::node("web1.exsample.com");
        Server::node("web2.exsample.com");
    }
}
