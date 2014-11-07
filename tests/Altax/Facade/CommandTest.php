<?php
namespace Test\Altax\Facade;

use Altax\Facade\Command;

class CommandTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
    }

    public function testDefault()
    {
        $object = Command::getFacadeRoot();
        $this->assertTrue($object instanceof \Altax\Shell\CommandBuilder);
    }
}
