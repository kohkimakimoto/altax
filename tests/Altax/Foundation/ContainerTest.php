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

    public function testSet()
    {
        $container = new Container();
        $container->set("nodes/web1", array("node" => "192.168.56.1"));
        $container->set("nodes/web2", array("node" => "192.168.56.1"));

        $container->set("roles/web/nodes", "web1");

//        print_r($container);
    }
}