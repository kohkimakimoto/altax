<?php
namespace Test\Kohkimakimoto\Altax;

use Kohkimakimoto\Altax\Context;
class ContextTest extends \PHPUnit_Framework_TestCase
{
    public function testInitialize()
    {
        $context = Context::initialize(__DIR__."/ContextTest/.altax/altax.php");
        $this->assertEquals(true, true);
    }

}