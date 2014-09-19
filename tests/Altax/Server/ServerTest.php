<?php
namespace Test\Altax\Server;

use Altax\Server\Server;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
    }

    public function testNode()
    {
        $server = $this->app["server"];

        $server->node("node1");
        $this->assertEquals("node1", $server->getNode("node1")->getName());

        $server->node("node2", "role1");
        $this->assertEquals("node2", $server->getNode("node2")->getName());
        $role1Nodes = $server->getRole("role1")->getNodes();
        $this->assertEquals("node2", $role1Nodes["node2"]->getName());

        $server->node("node3", ["role1", "role2"]);
        $this->assertEquals("node3", $server->getNode("node3")->getName());
        $role2Nodes = $server->getRole("role2")->getNodes();
        $this->assertEquals("node3", $role2Nodes["node3"]->getName());

        $server->node("node4", ["roles" => "role3"]);
        $this->assertEquals("node4", $server->getNode("node4")->getName());
        $role3Nodes = $server->getRole("role3")->getNodes();
        $this->assertEquals("node4", $role3Nodes["node4"]->getName());

        $server->node("node5", ["roles" => ["role4", "role5"]]);
        $this->assertEquals("node5", $server->getNode("node5")->getName());
        $role4Nodes = $server->getRole("role4")->getNodes();
        $role5Nodes = $server->getRole("role5")->getNodes();
        $this->assertEquals("node5", $role4Nodes["node5"]->getName());
        $this->assertEquals("node5", $role5Nodes["node5"]->getName());

        $server->node("node6", ["roles" => "role4"]);
        $role4Nodes = $server->getRole("role4")->getNodes();
        $this->assertSame(["node5", "node6"], array_keys($role4Nodes));

        $server->node("node7", ["username" => "kohkimakimoto"], "role7");
        $role7Nodes = $server->getRole("role7")->getNodes();
        $this->assertEquals("node7", $role7Nodes["node7"]->getName());
    }

    public function testRole()
    {
        $server = $this->app["server"];

        $server->role("role1", "node1");
        $role1Nodes = $server->getRole("role1")->getNodes();
        $this->assertEquals("node1", $role1Nodes["node1"]->getName());
    }

    public function testNodesFromSSHConfigHosts()
    {
        $server = $this->app["server"];
        $server->nodesFromSSHConfigHosts();
    }
}
