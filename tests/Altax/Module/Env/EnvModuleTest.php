<?php
namespace Test\Altax\Module\Env;

use Altax\Foundation\Container;
use Altax\Module\Env\EnvModule;

class ServerModuleTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->container = new Container();
    }
    
    public function testSetAndGet()
    {
        $module = new EnvModule($this->container);
        $module->set("aaa", "bbb");
        $this->assertEquals("bbb", $module->get("aaa"));
        $this->assertEquals(null, $module->get("aaab"));
    }
    
    public function testGetVars()
    {
        $module = new EnvModule($this->container);
        $module->set("aaa", "bbb");
        $module->set("bbb", "ccc");

        $vars = $module->getVars();

        $this->assertSame("bbb", $vars["aaa"]);
    }
}
