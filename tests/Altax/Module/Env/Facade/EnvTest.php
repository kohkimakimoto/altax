<?php
namespace Test\Altax\Module\Env\Facade;

use Altax\Foundation\Container;
use Altax\Foundation\ModuleFacade;
use Altax\Module\Env\Facade\Env;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->container = new Container();

        ModuleFacade::clearResolvedInstances();
        ModuleFacade::setContainer($this->container);

        $module = new \Altax\Module\Env\EnvModule($this->container);

        $this->container->addModule(Env::getModuleName(), $module);
    }

    public function testSetAndGet()
    {
        Env::set("aaa", "bbb");
        $this->assertEquals("bbb", Env::get("aaa"));

    }
}
