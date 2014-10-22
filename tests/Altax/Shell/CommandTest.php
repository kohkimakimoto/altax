<?php
namespace Test\Altax\Shell;

use Symfony\Component\Console\Output\BufferedOutput;

class CommandTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
        $this->app->instance("output", new BufferedOutput());
    }

    public function testMakeAndRunOnLoccally()
    {
        $commandBuilder = $this->app["shell.command"];
        $command = $commandBuilder->make("pwd");
        $command->run();

        $this->assertRegExp("/Run command locally:/", $this->app['output']->fetch());
    }

    public function testMakeAndRunOnRemotely()
    {
        $servers = $this->app['servers'];
        $servers->node("127.0.0.1");
        $env = $this->app['env'];
        $env->set('process.parallel', false);

        $executor = $this->app['process.executor'];
        $executor->on(["127.0.0.1"], function(){

            $commandBuilder = $this->app["shell.command"];
            $command = $commandBuilder->make("pwd");
            $command->run();

        });

        $this->assertRegExp("/Run command: pwd on 127.0.0.1/", $this->app['output']->fetch());
    }

    public function testRunOnLoccally()
    {
        $commandBuilder = $this->app["shell.command"];
        $commandBuilder->run("pwd");

        $this->assertRegExp("/Run command locally:/", $this->app['output']->fetch());
    }

    public function testRunOnRemotely()
    {
        $servers = $this->app['servers'];
        $servers->node("127.0.0.1");
        $env = $this->app['env'];
        $env->set('process.parallel', false);

        $executor = $this->app['process.executor'];
        $executor->on(["127.0.0.1"], function(){

            $commandBuilder = $this->app["shell.command"];
            $commandBuilder->run("pwd");

        });

        $this->assertRegExp("/Run command: pwd on 127.0.0.1/", $this->app['output']->fetch());
    }
}
