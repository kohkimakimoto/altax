<?php
namespace Test\Altax\Util;

use Altax\Util\Str;

class StrTest extends \PHPUnit_Framework_TestCase
{
    public function testCamel()
    {
        $this->assertEquals("aaaBbbCcc", Str::camel("aaa_bbb_ccc"));
    }

    public function testSnake()
    {
        $this->assertEquals("aaa_bbb_ccc", Str::snake("AaaBbbCcc"));
    }

    public function testStudly()
    {
        $this->assertEquals("AaaBbbCcc", Str::studly("aaa_bbb_ccc"));
    }

}