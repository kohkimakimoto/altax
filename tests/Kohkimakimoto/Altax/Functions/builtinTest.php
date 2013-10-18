<?php
namespace Test\Kohkimakimoto\Altax\Functions;

use Kohkimakimoto\Altax\Application\AltaxApplication;
use Kohkimakimoto\Altax\Util\Context;

class BuiltinFunctionsTest extends \PHPUnit_Framework_TestCase
{
    public function testSet()
    {
        $context = Context::initialize();

        set("test_val", "aaaaa");
        $this->assertEquals("aaaaa", $context->getParameter("test_val"));

    }

    public function testGet()
    {
        $context = Context::initialize();

        set("test_val", "ddddd");
        $this->assertEquals("ddddd", get("test_val"));

        set("test_val2", array("aaa" => array("bbb" => "ccc")));
        $this->assertEquals("ccc", get("test_val2/aaa/bbb"));
    }

}
