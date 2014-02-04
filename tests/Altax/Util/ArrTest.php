<?php
namespace Test\Altax\Util;

use Altax\Util\Arr;

class ArrTest extends \PHPUnit_Framework_TestCase
{
    public function testIsVector() {
        $this->assertEquals(true, Arr::isVector(array("1", "3", "aaaa")));
        $this->assertEquals(true, Arr::isVector(array(1, 2, 3)));
        $this->assertEquals(true, Arr::isVector(array(1, 2, "aaaa")));
        $this->assertEquals(true, Arr::isVector(array()));
        $this->assertEquals(false, Arr::isVector(array("a" => "bb", "c" => "dd")));
    }
}