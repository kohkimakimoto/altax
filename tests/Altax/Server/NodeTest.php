<?php
namespace Test\Altax\Server;

class NodeTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
    }

    public function testAccessors()
    {
        $serverManager = $this->app["servers"];
        $node = $serverManager->makeNode("test_node_name");

        $node->setName("test_node_name");
        $this->assertEquals("test_node_name", $node->getName());

        $this->assertEquals("test_node_name", $node->getHostOrDefault());
        $node->setHost("test.node.exsample.com");
        $this->assertEquals("test.node.exsample.com", $node->getHost());
        $this->assertEquals("test.node.exsample.com", $node->getHostOrDefault());

        $this->assertEquals(22, $node->getPortOrDefault());
        $node->setPort(2022);
        $this->assertEquals(2022, $node->getPort());
        $this->assertEquals(2022, $node->getPortOrDefault());

        $node->setDefaultKey("/path/to/default/private_key");
        $this->assertEquals("/path/to/default/private_key", $node->getDefaultKey());
        $this->assertEquals("/path/to/default/private_key", $node->getKeyOrDefault());

        $node->setKey("/path/to/private_key");
        $this->assertEquals("/path/to/private_key", $node->getKey());
        $this->assertEquals("/path/to/private_key", $node->getKeyOrDefault());

        $node->setDefaultUsername("default_ssh_connection_user");
        $this->assertEquals("default_ssh_connection_user", $node->getDefaultUsername());
        $this->assertEquals("default_ssh_connection_user", $node->getUsernameOrDefault());

        $node->setUsername("ssh_connection_user");
        $this->assertEquals("ssh_connection_user", $node->getUsername());
        $this->assertEquals("ssh_connection_user", $node->getUsernameOrDefault());
    }

    public function testReplaceTilda()
    {
        $serverManager = $this->app["servers"];
        $node = $serverManager->makeNode("test_node_name");

        $orgHome = getenv("HOME");
        putenv("HOME=/home/your");
        $node->setKey("~/path/to/private_key");
        $this->assertEquals("~/path/to/private_key", $node->getKey());
        $this->assertEquals("/home/your/path/to/private_key", $node->getKeyOrDefault());

        $node->setKey("~user/path/to/private_key");
        $this->assertEquals("~user/path/to/private_key", $node->getKey());
        $this->assertEquals("~user/path/to/private_key", $node->getKeyOrDefault());

        $node->setKey("~");
        $this->assertEquals("~", $node->getKey());
        $this->assertEquals("/home/your", $node->getKeyOrDefault());

        putenv("HOME=/home/your\\0");
        $node->setKey("~/path/to/private_key");
        $this->assertEquals("~/path/to/private_key", $node->getKey());
        $this->assertEquals("/home/your\\0/path/to/private_key", $node->getKeyOrDefault());

        $node->setKey("/path/to/private_key~");
        $this->assertEquals("/path/to/private_key~", $node->getKey());
        $this->assertEquals("/path/to/private_key~", $node->getKeyOrDefault());

        putenv("HOME=$orgHome");
    }
}