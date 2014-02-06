<?php
namespace Test\Altax\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Tester\ApplicationTester;
use Altax\Command\ClosureTaskCommand;
use Altax\Console\Application;
use Altax\Foundation\Container;
use Altax\Module\Task\Resource\DefinedTask;

class ClosureTaskCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testDefault()
    {
        $container = new Container();
        $task = new DefinedTask();
        $task->setName("test");
        $task->setDescription("Description of the test task");
        $task->setClosure(function($task){

            $task->getOutput()->write("test message for closure task command");

        });

        $application = new Application($container);
        $application->setAutoExit(false);
        $application->add(new ClosureTaskCommand($task));
        $command = $application->find("test");

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array("command" => $command->getName())
            );

        $this->assertEquals("test message for closure task command", $commandTester->getDisplay());
    }
}
