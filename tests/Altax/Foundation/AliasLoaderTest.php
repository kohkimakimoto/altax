<?php
namespace Test\Altax\Foundation;

use Altax\Foundation\AliasLoader;

class AliasLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $loader = AliasLoader::getInstance();
        $loader->setPrefix(null);
        $loader->register();
    }

    public function testRegister()
    {
        AliasLoader::getInstance([
            "TestAAABBB" => "Test\Altax\Foundation\TestSourceClass",
        ])->register();
        $obj = new \TestAAABBB();
        $this->assertTrue($obj instanceof \Test\Altax\Foundation\TestSourceClass);
    }

    public function testPrefix()
    {
        $loader = AliasLoader::getInstance([
            "TestAAABBB2" => "Test\Altax\Foundation\TestSourceClass",
        ]);
        $loader->register();

        $loader->setPrefix("TestPrefix");

        $obj = new \TestPrefixTestAAABBB2();
        $this->assertTrue($obj instanceof \Test\Altax\Foundation\TestSourceClass);

        $this->assertEquals("TestPrefix", $loader->getPrefix());
    }

    public function testAliases()
    {
        $loader = AliasLoader::getInstance();
        $loader->setAliases([
            "TestAAABBB3" => "Test\Altax\Foundation\TestSourceClass",
            ]);
        $obj = new \TestAAABBB3();
        $this->assertTrue($obj instanceof \Test\Altax\Foundation\TestSourceClass);

        $this->assertEquals([
            "TestAAABBB3" => "Test\Altax\Foundation\TestSourceClass",
        ], $loader->getAliases());

        $loader->alias("TestAAABBB4", "Test\Altax\Foundation\TestSourceClass");
        $obj = new \TestAAABBB4();
        $this->assertTrue($obj instanceof \Test\Altax\Foundation\TestSourceClass);
    }

    public function testSetInstance()
    {
        AliasLoader::setInstance(new AliasLoader());
    }

    public function testSetRegistered()
    {
        $loader = AliasLoader::getInstance();
        $loader->setRegistered(true);

        $this->assertTrue($loader->isRegistered());
    }
}

class TestSourceClass
{
}
