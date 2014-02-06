<?php
namespace Test\Altax\Foundation;

class bootTest extends \PHPUnit_Framework_TestCase
{
    public function testDefault()
    {
        // load basic boot file to create container
        $container = require_once __DIR__.'/../../../src/Altax/Foundation/boot.php';
        $this->assertEquals("Altax\Foundation\Container", get_class($container));
    }
}