<?php
namespace Test\Altax\Module\Server\Resource;

use Altax\Foundation\Container;
use Altax\Foundation\ModuleFacade;
use Altax\Module\Server\Facade\Server;
use Altax\Module\Server\Resource\Node;
use Altax\Module\Env\Facade\Env;

class NodeTest extends \PHPUnit_Framework_TestCase
{
    private $originalHome;

    protected function setUp()
    {
        $this->container = new Container();

        ModuleFacade::clearResolvedInstances();
        ModuleFacade::setContainer($this->container);

        $module = new \Altax\Module\Server\ServerModule($this->container);
        $this->container->addModule(Server::getModuleName(), $module);

        $module = new \Altax\Module\Env\EnvModule($this->container);
        $this->container->addModule(Env::getModuleName(), $module);

        $this->originalHome = getenv('HOME');
    }

    protected function tearDown()
    {
        if ($this->originalHome !== false)
        {
            putenv("HOME=$this->originalHome");
        }
    }

    public function testAccessors()
    {
        $node = new Node();

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

        try {
            $node->setOptions("aaa");
            $this->assertEquals(fales, true);
        } catch (\RuntimeException $e) {
            $this->assertEquals(true, true);
        }
    }

    public function testReplaceTilda()
    {
        $node = new Node();

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
    }

}
