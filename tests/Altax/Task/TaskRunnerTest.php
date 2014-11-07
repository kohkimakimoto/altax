<?php
namespace Test\Altax\Task;

use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Tester\CommandTester;
use Altax\Console\Application;

class TaskRunnerTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
        $this->app->instance("output", new BufferedOutput());
    }

    public function testCall()
    {
        $application = new Application($this->app);
        $application->setAutoExit(false);

        $taskManager = $this->app["task"];
        $task = $taskManager->register("test", function(){

            $this->app["task_runner"]->call("test2", ["hogehoge"]);

        });
        $task2 = $taskManager->register("test2", function($foo = "default"){

            $this->app["output"]->write($foo);
            $this->app["task_runner"]->call("test3");
            $this->app["task_runner"]->call("test3", ["v" => "hoge"]);
            $this->app["task_runner"]->call("test3", ["foo"]);

        });
        $task3 = $taskManager->register("test3", function($v = "aaa", $v2 = "bbb"){

            $this->app["output"]->write($v);

        });

        $application->add($task->makeCommand());
        $application->add($task2->makeCommand());
        $application->add($task3->makeCommand());

        $command = $application->find("test");
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                "command" => $command->getName(),
                )
            );
        $this->assertRegExp("/hogehogeaaahogefoo/", $this->app['output']->fetch());

    }

}
