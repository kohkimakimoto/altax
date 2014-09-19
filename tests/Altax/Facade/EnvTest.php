<?php
namespace Test\Altax\Facade;

use Altax\Facade\Env;

class EnvTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
    }

    public function testDefault()
    {
        $object = Env::getFacadeRoot();
        $this->assertTrue($object instanceof \Altax\Env\Env);
    }
}
