<?php
namespace Test\Altax\Facade;

use Altax\Facade\Input;

class InputTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
    }

    public function testDefault()
    {
        $object = Input::getFacadeRoot();
        $this->assertTrue($object instanceof \Symfony\Component\Console\Input\Input);
    }
}
