<?php
namespace Test\Altax\Application;

use Altax\Application\Application;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testSetConfigFIle()
    {
        $app = new Application();
        $app->setConfigFile("home", "path/to/config.php");
        $this->assertEquals("path/to/config.php", $app->getConfigFile("home"));
    }
}