<?php
namespace Test\Altax\Application;

use Altax\Application\Application;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function testSetConfigFIle()
    {
        $app = new Application();
        $app->setConfigFile("home", "path/to/config.php");
        $this->assertEquals("path/to/config.php", $app->getConfigFile("home"));
    }

    public function testGetConfigFIles()
    {
        $app = new Application();
        $app->setConfigFile("home", "path/to/config.php");
        $app->setConfigFile("default", "path/to/default/config.php");

        $this->assertEquals(array(
            "home" => "path/to/config.php",
            "default" => "path/to/default/config.php",
                ), 
            $app->getConfigFiles());
    }
}