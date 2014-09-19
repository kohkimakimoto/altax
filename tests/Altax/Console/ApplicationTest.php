<?php
namespace Test\Altax\Console;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Tester\ApplicationTester;
use Altax\Console\Application;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
    }

    public function testListCommand()
    {
        $application = new Application($this->app);
        $application->setAutoExit(false);

        $applicationTester = new ApplicationTester($application);
        $applicationTester->run(array("command" => "list"));

        $output = $applicationTester->getDisplay();
        $this->assertRegExp("/Available commands:/", $output);
    }
}