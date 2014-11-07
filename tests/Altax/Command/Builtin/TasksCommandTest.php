<?php
namespace Test\Altax\Command\Builtin;

use Symfony\Component\Console\Tester\CommandTester;
use Altax\Command\Builtin\TasksCommand;
use Altax\Console\Application;
use Task;

class TasksCommandTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
    }

    public function testNotfound()
    {
        $application = new Application($this->app);
        $application->setAutoExit(false);
        $application->add(new TasksCommand());
        $command = $application->find("tasks");

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                "command" => $command->getName(),
                )
            );

        $this->assertEquals("No tasks defined.\n", $commandTester->getDisplay());
    }

    public function testDefault()
    {
        $application = new Application($this->app);
        $application->setAutoExit(false);
        $application->add(new TasksCommand());
        $command = $application->find("tasks");

        Task::register("test", function(){});

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                "command" => $command->getName(),
                )
            );

        $expected = <<<EOL
name    description    
test                   

EOL;
        $this->assertEquals($expected, $commandTester->getDisplay());
    }
}