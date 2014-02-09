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

    public function testSetAndGet()
    {
        $container = new Container();
        $container->set("a", "aaa");
        $container->set("b/c", "BBB");

        $this->assertEquals("aaa", $container->get("a"));
        $this->assertEquals("BBB", $container->get("b/c"));

    }

    public function testSetAndGetModule()
    {
        $container = new Container();
        $container->setModules(array("abc" => "def"));
        $this->assertSame(array("abc" => "def"), $container->getModules());
    }

    public function testDelete()
    {
        $container = new Container();
        $container->set("foo", "bar");
        $container->delete("foo");
        $this->assertEquals(null, $container->get("foo"));
    }

    public function testArrayAccess()
    {
        $container = new Container();
        $container->set("foo", "bar");
        $this->assertEquals("bar", $container["foo"]);

        unset($container["foo"]);
        $this->assertEquals(false, isset($container["foo"]));

        $container["foo"] = "aaaa";   
        $this->assertEquals("aaaa", $container["foo"]);
    }

    public function testIterator()
    {

        $container = new Container();
        $container->set("foo", "bar");
        $container->set("foo1", "bar1");
        $container->set("foo2", "bar2");
        $container->set("foo3"," bar3");
        $container->set("foo4", "bar4");

        $i = 0;
        $status = 0;
        foreach ($container as $k => $v) {
            if ($i == 0) {
                $this->assertEquals("foo", $k);
                $status++;
            }
            if ($i == 1) {
                $this->assertEquals("foo1", $k); 
                $status++;
            }
            if ($i == 2) {
                $this->assertEquals("foo2", $k); 
                $status++;
            }
            if ($i == 3) {
                $this->assertEquals("foo3", $k); 
                $status++;
            }
            if ($i == 4) {
                $this->assertEquals("foo4", $k); 
                $status++;
            }
            $i++;
        }

        $this->assertEquals(5, $status);
        $this->assertEquals(5, count($container));
    }
}