<?php
namespace Test\Altax\Command\Builtin;

use Symfony\Component\Console\Tester\CommandTester;
use Altax\Command\Builtin\EnvCommand;
use Altax\Console\Application;

class EnvCommandTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
    }

    public function testCommand()
    {
        $application = new Application($this->app);
        $application->setAutoExit(false);
        $application->add(new EnvCommand());
        $command = $application->find("env");
        $commandTester = new CommandTester($command);
        $commandTester->execute(["command" => $command->getName()]);

        $this->assertRegExp("/^key.*value/", $commandTester->getDisplay());
    }

    public function testCommandJsonFormat()
    {
        $application = new Application($this->app);
        $application->setAutoExit(false);
        $application->add(new EnvCommand());
        $command = $application->find("env");
        $commandTester = new CommandTester($command);
        $commandTester->execute(["command" => $command->getName(), "--format" => "json"]);

        $this->assertRegExp("/^{.*}/", $commandTester->getDisplay());
    }
}
