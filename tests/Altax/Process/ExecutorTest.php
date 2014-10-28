<?php
namespace Test\Altax\Process;

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

    public function testOn1()
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

    public function testOn2()
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

    public function testOn3()
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

    public function testOn4()
    {
        $output = $this->app['output'];
        $output->setVerbosity(4);

        $executor = $this->app["process.executor"];
        try {
            $executor->on("", "");
            $this->assertEquals(false, true);
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(true, true);
        }
    }

    public function testOnParallel()
    {
        $this->app['env']->set('process.parallel', false);
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

    public function testExec1()
    {
        $output = $this->app['output'];
        $output->setVerbosity(4);

        $executor = $this->app["process.executor"];
        $executor->exec(["e1", "e2"], function(){

        });

        $contents = $output->fetch();
        $this->assertRegExp("/Found 2 entries:/", $contents);
    }

    public function testExec2()
    {
        $output = $this->app['output'];
        $output->setVerbosity(4);

        $server = $this->app['servers'];
        $server->node("127.0.0.1", "test");
        $server->node("localhost", "test");

        $executor = $this->app["process.executor"];
        $executor->exec("e1", function(){

        });

        $contents = $output->fetch();
        $this->assertRegExp("/Found 1 entries:/", $contents);
    }

    public function testExec3()
    {
        $output = $this->app['output'];
        $output->setVerbosity(4);

        $executor = $this->app["process.executor"];
        try {
            $executor->exec("", "");
            $this->assertEquals(false, true);
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(true, true);
        }
    }

    public function testExecParallel()
    {
        $this->app['env']->set('process.parallel', false);
        $output = $this->app['output'];
        $output->setVerbosity(4);

        $server = $this->app['servers'];
        $server->node("127.0.0.1", "test");
        $server->node("localhost", "test");

        $executor = $this->app["process.executor"];
        $executor->exec("e1", function(){

        });

        $contents = $output->fetch();
        $this->assertRegExp("/Found 1 entries:/", $contents);
    }

}