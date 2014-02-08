<?php
namespace Test\Altax\Module\Server\Resource;

use Altax\Foundation\Container;
use Altax\Foundation\ModuleFacade;
use Altax\Module\Server\Facade\Server;
use Altax\Module\Server\Resource\Node;

class NodeTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->container = new Container();

        ModuleFacade::clearResolvedInstances();
        ModuleFacade::setContainer($this->container);

        $module = new \Altax\Module\Server\ServerModule($this->container);

        $this->container->addModule(Server::getModuleName(), $module);
    }

    public function testAccessors()
    {
        $node = new Node();

        $node->setName("test_node_name");
        $this->assertEquals("test_node_name", $node->getName());
        
        $node->setHost("test.node.exsample.com");
        $this->assertEquals("test.node.exsample.com", $node->getHost());
  
        $node->setPort(2022);
        $this->assertEquals(2022, $node->getPort());

        $node->setKey("/path/to/private_key");
        $this->assertEquals("/path/to/private_key", $node->getKey());

        $node->setUsername("ssh_connection_user");
        $this->assertEquals("ssh_connection_user", $node->getUsername());
    }

}