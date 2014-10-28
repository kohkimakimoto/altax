<?php
namespace Test\Altax\Shell;

use Symfony\Component\Console\Output\BufferedOutput;

class LocalCommandTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
        $this->app->instance("output", new BufferedOutput());
    }

    public function testMakeAndRun()
    {
        $commandBuilder = $this->app["shell.local_command"];
        $command = $commandBuilder->make("pwd");
        $command->run();
        $this->assertRegExp("/Run local command: pwd/", $this->app['output']->fetch());
    }

    public function testRun()
    {
        $commandBuilder = $this->app["shell.local_command"];
        $command = $commandBuilder->run("pwd");
        $this->assertRegExp("/Run local command: pwd/", $this->app['output']->fetch());
    }

    public function testRunWithOptions()
    {
        $commandBuilder = $this->app["shell.local_command"];
        $command = $commandBuilder->run("pwd", [
            "cwd" => __DIR__,
            "user" => get_current_user(),
            "timeout" => 100,
            ]);
        $this->assertRegExp("/Run local command: pwd/", $this->app['output']->fetch());
    }

}
