<?php
namespace Test\Altax\Module\Server;

use Altax\Foundation\Container;
use Altax\Foundation\ModuleFacade;
use Altax\Module\Server\ServerModule;

class ServerModuleTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->container = new Container();
    }

    public function testNode()
    {
        $module = new ServerModule($this->container);

        $module->node("node1");
        $this->assertEquals("node1", $module->getNode("node1")->getName());

        $module->node("node2", "role1");
        $this->assertEquals("node2", $module->getNode("node2")->getName());
        $this->assertSame(array("node2" => "node2"), $module->getRole("role1"));

        $module->node("node3", array("role1", "role2"));
        $this->assertEquals("node3", $module->getNode("node3")->getName());
        $this->assertSame(array("node3" => "node3"), $module->getRole("role2"));

        $module->node("node4", array("roles" => "role3"));
        $this->assertEquals("node4", $module->getNode("node4")->getName());
        $this->assertSame(array("node4" => "node4"), $module->getRole("role3"));

        $module->node("node5", array("roles" => array("role4", "role5")));
        $this->assertEquals("node5", $module->getNode("node5")->getName());
        $this->assertSame(array("node5" => "node5"), $module->getRole("role4"));
        $this->assertSame(array("node5" => "node5"), $module->getRole("role5"));

        $module->node("node6", array("roles" => "role4"));
        $this->assertSame(array("node5" => "node5", "node6" => "node6"), $module->getRole("role4"));

        $module->node("node7", array("username" => "kohkimakimoto"), "role7");
        $this->assertSame(array("node7" => "node7"), $module->getRole("role7"));
    }

    public function testRole()
    {
        $module = new ServerModule($this->container);

        $module->role("role1", "node1");
        $this->assertSame(array("node1" => "node1"), $module->getRole("role1"));
        $this->assertSame("node1", $module->getNode("node1")->getName());

    }

    public function testNodesFromSSHConfigHosts()
    {
        $module = new ServerModule($this->container);
        $module->nodesFromSSHConfigHosts();
    }
}