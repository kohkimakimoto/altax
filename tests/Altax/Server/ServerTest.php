<?php
namespace Test\Altax\Server;

use Altax\Server\ServerManager;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
    }

    public function testNode()
    {
        $servers = $this->app["servers"];

        $servers->node("node1");
        $this->assertEquals("node1", $servers->getNode("node1")->getName());

        $servers->node("node2", "role1");
        $this->assertEquals("node2", $servers->getNode("node2")->getName());
        $role1Nodes = $servers->getRole("role1")->getNodes();
        $this->assertEquals("node2", $role1Nodes["node2"]->getName());

        $servers->node("node3", ["role1", "role2"]);
        $this->assertEquals("node3", $servers->getNode("node3")->getName());
        $role2Nodes = $servers->getRole("role2")->getNodes();
        $this->assertEquals("node3", $role2Nodes["node3"]->getName());

        $servers->node("node4", ["roles" => "role3"]);
        $this->assertEquals("node4", $servers->getNode("node4")->getName());
        $role3Nodes = $servers->getRole("role3")->getNodes();
        $this->assertEquals("node4", $role3Nodes["node4"]->getName());

        $servers->node("node5", ["roles" => ["role4", "role5"]]);
        $this->assertEquals("node5", $servers->getNode("node5")->getName());
        $role4Nodes = $servers->getRole("role4")->getNodes();
        $role5Nodes = $servers->getRole("role5")->getNodes();
        $this->assertEquals("node5", $role4Nodes["node5"]->getName());
        $this->assertEquals("node5", $role5Nodes["node5"]->getName());

        $servers->node("node6", ["roles" => "role4"]);
        $role4Nodes = $servers->getRole("role4")->getNodes();
        $this->assertSame(["node5", "node6"], array_keys($role4Nodes));

        $servers->node("node7", ["username" => "kohkimakimoto"], "role7");
        $role7Nodes = $servers->getRole("role7")->getNodes();
        $this->assertEquals("node7", $role7Nodes["node7"]->getName());
    }

    public function testRole()
    {
        $servers = $this->app["servers"];

        $servers->role("role1", "node1");
        $role1Nodes = $servers->getRole("role1")->getNodes();
        $this->assertEquals("node1", $role1Nodes["node1"]->getName());
    }

    public function testNodesFromSSHConfigHosts()
    {
        $servers = $this->app["servers"];
        $servers->nodesFromSSHConfigHosts();
    }
}
