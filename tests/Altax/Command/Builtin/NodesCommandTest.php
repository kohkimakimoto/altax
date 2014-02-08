<?php
namespace Test\Altax\Command\Builtin;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Tester\ApplicationTester;
use Altax\Command\Builtin\NodesCommand;
use Altax\Console\Application;
use Altax\Foundation\Container;
use Altax\Foundation\ModuleFacade;
use Altax\Module\Task\Resource\DefinedTask;
use Altax\Module\Server\Facade\Server;

class NodesCommandTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->container = new Container();

        ModuleFacade::clearResolvedInstances();
        ModuleFacade::setContainer($this->container);

        $module = new \Altax\Module\Server\ServerModule($this->container);

        $this->container->addModule(Server::getModuleName(), $module);

    }

    public function testNotfound()
    {
        $application = new Application($this->container);
        $application->setAutoExit(false);
        $application->add(new NodesCommand());
        $command = $application->find("nodes");
        
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                "command" => $command->getName(),
                )
            );

        $this->assertEquals("There are not any nodes.\n", $commandTester->getDisplay());
    }

    public function testDefault()
    {
        $application = new Application($this->container);
        $application->setAutoExit(false);
        $application->add(new NodesCommand());
        $command = $application->find("nodes");

        Server::node("web1.example.com");
        Server::node("web2.example.com");
        Server::node("web3.example.com");
        
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                "command" => $command->getName(),
                )
            );

        $expected = <<<EOL
+------------------+-------+
| name             | roles |
+------------------+-------+
| web1.example.com |       |
| web2.example.com |       |
| web3.example.com |       |
+------------------+-------+

EOL;
        $this->assertEquals($expected, $commandTester->getDisplay());
    }

    public function testDetailOutput()
    {
        $application = new Application($this->container);
        $application->setAutoExit(false);
        $application->add(new NodesCommand());
        $command = $application->find("nodes");

        Server::node("web1.example.com");
        Server::node("web2.example.com");
        Server::node("web3.example.com");
        
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                "command" => $command->getName(),
                "--detail" => true,
                )
            );

        $expected = <<<EOL
+------------------+------+------+----------+-----+-------+
| name             | host | port | username | key | roles |
+------------------+------+------+----------+-----+-------+
| web1.example.com |      |      |          |     |       |
| web2.example.com |      |      |          |     |       |
| web3.example.com |      |      |          |     |       |
+------------------+------+------+----------+-----+-------+

EOL;
        $this->assertEquals($expected, $commandTester->getDisplay());
    }
}
