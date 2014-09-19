<?php
namespace Test\Altax\Foundation;

class FunctionTest extends \PHPUnit_Framework_TestCase
{
    public function testBootAltaxApplication()
    {
        $app = bootAltaxApplication();


    }

    public function testBootAltaxApplicationNotCli()
    {
        $app = bootAltaxApplication(array(), false);
    }

    public function testIsVector()
    {
        $this->assertTrue(is_vector(array("aa", "errr", "cccc")));
        $this->assertFalse(is_vector(array("aa" => "bbb", "ccc" => "ddd")));

    }
}
