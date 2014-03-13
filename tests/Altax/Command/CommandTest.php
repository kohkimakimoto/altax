<?php
namespace Test\Altax\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Tester\ApplicationTester;
use Altax\Command\Command;
use Altax\Console\Application;
use Altax\Foundation\Container;
use Altax\Module\Task\Resource\DefinedTask;

class CommandTest extends \PHPUnit_Framework_TestCase
{
    public function testDefault()
    {
        $container = new Container();
        $task = new DefinedTask();
        $task->setName("test");
        $task->setDescription("Description of the test task");

        $application = new Application($container);
        $application->setAutoExit(false);
        $application->add(new TestCommand($task));
        $command = $application->find("test");

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array("command" => $command->getName(), "--verbose" => 3)
            );

        $this->assertEquals("test message", $commandTester->getDisplay());
    }

    public function testOverrideDescription()
    {
        $container = new Container();
        $task = new DefinedTask();
        $task->setName("test");
        $task->setDescription("custom description");

        $application = new Application($container);
        $application->setAutoExit(false);
        $application->add(new TestCommand($task));
        $command = $application->find("test");

        $this->assertEquals("custom description", $command->getDescription());
    }
}

class TestCommand extends \Altax\Command\Command
{
    protected function configure()
    {
        $this->setDescription("default description");
    }

    protected function fire($task)
    {
        $task->getOutput()->write("test message");
    }
}
