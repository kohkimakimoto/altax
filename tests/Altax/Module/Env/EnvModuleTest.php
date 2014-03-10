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
    
    public function testSet()
    {
        $module = new EnvModule($this->container);
        $module->set("aaa", "bbb");
    }

}
