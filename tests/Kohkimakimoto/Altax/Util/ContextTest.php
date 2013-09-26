<?php
namespace Test\Kohkimakimoto\Altax\Util;

use Kohkimakimoto\Altax\Util\Context;

class ContextTest extends \PHPUnit_Framework_TestCase
{
    public function testInitialize()
    {
        $context = Context::initialize(__DIR__."/ContextTest/.altax/altax.php");
        $this->assertEquals(true, true);
    }

    public function testGetInstance()
    {
    }

}