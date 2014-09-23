<?php
namespace Test\Altax\Command\Builtin;

use Symfony\Component\Console\Tester\CommandTester;
use Altax\Command\Builtin\NodesCommand;
use Altax\Console\Application;
use Server;

class NodesCommandTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
    }

    public function testNotfound()
    {
        $application = new Application($this->app);
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
        $application = new Application($this->app);
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
name                roles    
web1.example.com             
web2.example.com             
web3.example.com             

EOL;
        $this->assertEquals($expected, $commandTester->getDisplay());
    }
}
