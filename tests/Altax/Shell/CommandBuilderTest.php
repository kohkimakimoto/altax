<?php
namespace Test\Altax\Shell;

use Symfony\Component\Console\Output\BufferedOutput;

class CommandBuilderTest extends \PHPUnit_Framework_TestCase
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
}
