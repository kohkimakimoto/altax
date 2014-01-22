<?php
namespace Test\Altax\Foundation;

use Altax\Foundation\Container;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testSetConfigFIle()
    {
        $container = new Container();
        $container->setConfigFile("home", "path/to/config.php");
        $this->assertEquals("path/to/config.php", $container->getConfigFile("home"));
    }

    public function testGetConfigFIles()
    {
        $container = new Container();
        $container->setConfigFile("home", "path/to/config.php");
        $container->setConfigFile("default", "path/to/default/config.php");

        $this->assertEquals(array(
            "home" => "path/to/config.php",
            "default" => "path/to/default/config.php",
                ), 
            $container->getConfigFiles());
    }
}