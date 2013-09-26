<?php
namespace Test\Kohkimakimoto\Altax\Util;

use Kohkimakimoto\Altax\Util\Context;

class ContextTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateInstance()
    {
        //$context = Context::initialize(__DIR__."/ContextTest/.altax/altax.php");
        $context = Context::createInstance();
        $this->assertEquals(true, true);
    }

    public function testGetInstance()
    {
        //$context = Context::initialize(__DIR__."/ContextTest/.altax/altax.php");
        $context = Context::createInstance();
        Context::getInstance();
    }

    public function testSetAndGetAndDelete()
    {
        //$context = Context::initialize(__DIR__."/ContextTest/.altax/altax.php");
        $context = Context::createInstance();
        $context->set("param1", "hogehoge");
        $this->assertEquals("hogehoge", $context->get("param1"));
        $context->delete("param1");
        $this->assertEquals(null, $context->get("param1"));
    }
}