<?php
namespace Test\Altax\Command\Builtin;

use Symfony\Component\Console\Tester\CommandTester;
use Altax\Command\Builtin\RolesCommand;
use Altax\Console\Application;
use Server;

class RolesCommandTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
    }

    public function testNotfound()
    {
        $application = new Application($this->app);
        $application->setAutoExit(false);
        $application->add(new RolesCommand());
        $command = $application->find("roles");

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                "command" => $command->getName(),
                )
            );

        $this->assertEquals("There are not any roles.\n", $commandTester->getDisplay());
    }

    public function testDefault()
    {
        $application = new Application($this->app);
        $application->setAutoExit(false);
        $application->add(new RolesCommand());
        $command = $application->find("roles");

        Server::node("web1.example.com", "web");
        Server::node("web2.example.com", "web");
        Server::node("db1.example.com", "db");

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                "command" => $command->getName(),
                )
            );

        $expected = <<<EOL
name    nodes                                
web     web1.example.com,web2.example.com    
db      db1.example.com                      

EOL;
        $this->assertEquals($expected, $commandTester->getDisplay());
    }
}
