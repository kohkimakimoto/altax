<?php
namespace Test\Altax\Facade;

use Altax\Facade\App;

class AppTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
    }

    public function testDefault()
    {
        $object = App::getFacadeRoot();
        $this->assertTrue($object instanceof \Altax\Foundation\Application);
    }
}
