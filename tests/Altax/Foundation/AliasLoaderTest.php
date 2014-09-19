<?php
namespace Test\Altax\Foundation;

use Altax\Foundation\AliasLoader;

class AliasLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testRegister()
    {
        AliasLoader::getInstance(array(
            "TestAAABBB" => "Test\Altax\Foundation\TestSourceClass",
        ))->register();

        $obj = new \TestAAABBB();
        $this->assertTrue($obj instanceof \Test\Altax\Foundation\TestSourceClass);
    }
}

class TestSourceClass
{
}