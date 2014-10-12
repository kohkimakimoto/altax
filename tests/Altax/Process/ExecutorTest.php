<?php
namespace Test\Altax\Executor;

use Symfony\Component\Console\Output\BufferedOutput;
use Altax\Process\Executor;
use Altax\Command\ClosureTaskCommand;

class ExecutorTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
        $this->app->instance('output', new BufferedOutput());

        $taskManager = $this->app["task"];
        $task = $taskManager->register("test", function(){});
        $this->app->instance('command', $task->makeCommand());
    }

    public function testExecute1()
    {
        $output = $this->app['output'];
        $output->setVerbosity(4);

        $server = $this->app['servers'];
        $server->node("127.0.0.1", "test");
        $server->node("localhost", "test");

        $executor = $this->app["process.executor"];
        $executor->on(["test"], function(){

        });

        $contents = $output->fetch();
        $this->assertRegExp("/Found 2 nodes:/", $contents);
    }

    public function testExecute2()
    {
        $output = $this->app['output'];
        $output->setVerbosity(4);

        $server = $this->app['servers'];
        $server->node("127.0.0.1", "test");
        $server->node("localhost", "test");

        $executor = $this->app["process.executor"];
        $executor->on("127.0.0.1", function(){

        });

        $contents = $output->fetch();
        $this->assertRegExp("/Found 1 nodes:/", $contents);
    }

    public function testExecute3()
    {
        $output = $this->app['output'];
        $output->setVerbosity(4);

        $server = $this->app['servers'];
        $server->node("127.0.0.1", "test");
        $server->node("localhost", "test");
        $server->node("nodeIsSameNameOfRole", "nodeIsSameNameOfRole");

        $executor = $this->app["process.executor"];
        try {
            $executor->on("nodeIsSameNameOfRole", function(){

            });
            $this->assertEquals(false, true);
        } catch (\RuntimeException $e) {
            $this->assertEquals(true, true);
        }

    }
}