<?php
namespace Test\Altax\Command\Builtin;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Tester\ApplicationTester;
use Altax\Command\Builtin\TasksCommand;
use Altax\Console\Application;
use Altax\Foundation\Container;
use Altax\Foundation\ModuleFacade;
use Altax\Module\Task\Resource\DefinedTask;
use Altax\Module\Task\Facade\Task;

class TasksCommandTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->container = new Container();

        ModuleFacade::clearResolvedInstances();
        ModuleFacade::setContainer($this->container);

        $module = new \Altax\Module\Task\TaskModule($this->container);

        $this->container->addModule(Task::getModuleName(), $module);

    }

    public function testNotfound()
    {
        $application = new Application($this->container);
        $application->setAutoExit(false);
        $application->add(new TasksCommand());
        $command = $application->find("tasks");
        
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                "command" => $command->getName(),
                )
            );

        $this->assertEquals("There are not any tasks.\n", $commandTester->getDisplay());
    }

    public function testDefault()
    {
        $application = new Application($this->container);
        $application->setAutoExit(false);
        $application->add(new TasksCommand());
        $command = $application->find("tasks");

        Task::register("sample1", function(){});
        Task::register("sample2", function(){});

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                "command" => $command->getName(),
                )
            );

        $expected = <<<EOL
+---------+-------------+
| name    | description |
+---------+-------------+
| sample1 |             |
| sample2 |             |
+---------+-------------+

EOL;
        $this->assertEquals($expected, $commandTester->getDisplay());
    }
}
