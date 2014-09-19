<?php
namespace Test\Altax\Foundation;

use Altax\Foundation\Application;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function testGetName()
    {
        $app = new Application();
        $this->assertEquals("Altax", $app->getName());
    }

    public function testGetVersionWithCommit()
    {
        $app = new Application();
        $this->assertEquals("3.1.0 - %commit%", $app->getVersionWithCommit());
    }

    public function testRegisterBuiltinAliases()
    {
        $app = new Application();
        $app->registerBuiltinAliases();
    }

    public function testRegisterBuiltinProviders()
    {
        $app = new Application();
        $app->registerBuiltinProviders();
    }
}
