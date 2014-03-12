<?php
namespace Test\Altax\Console;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Tester\ApplicationTester;
use Altax\Console\Application;
use Altax\Foundation\Container;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->container = new Container();
        $this->container->setConfigFiles(array(
            "current", __DIR__."/ApplicationTest/config.php",
        ));
    }

    public function testListCommand()
    {
        $application = new Application($this->container);
        $application->setAutoExit(false);

        $applicationTester = new ApplicationTester($application);
        $applicationTester->run(array("command" => "list"));

        $output = $applicationTester->getDisplay();
        $this->assertRegExp("/Available commands:/", $output);
    }

    public function testTestBasicCommand()
    {
        $application = new Application($this->container);
        $application->setAutoExit(false);

        $applicationTester = new ApplicationTester($application);
        $applicationTester->run(array("command" => "testBasic", "--verbose" => 3));

        $output = $applicationTester->getDisplay();
        $this->assertRegExp("/output log/", $output);
        $this->assertRegExp("/Run testHidden!/", $output);
        $this->assertRegExp("/runLocally!/", $output);
        $this->assertRegExp("/Node is not defined to run the command./", $output);

//        echo $output;
    }

    public function testTestBeforeAndAfter1Command()
    {
        $application = new Application($this->container);
        $application->setAutoExit(false);

        $applicationTester = new ApplicationTester($application);
        $applicationTester->run(array("command" => "testBeforeAndAfter1", "--verbose" => 3));
        $output = $applicationTester->getDisplay();
        $this->assertRegExp("/before!((?:.|\n)+)hello!((?:.|\n)+)after!/", $output);

//        echo $output;
    }

    public function testRegisterCommand()
    {
        $application = new Application($this->container);
        $application->setAutoExit(false);

        $applicationTester = new ApplicationTester($application);
        $applicationTester->run(array("command" => "testRegisterCommand", "--verbose" => 3));
        $output = $applicationTester->getDisplay();
        $this->assertRegExp("/Fired test01 command task!/", $output);
    }

    public function testTestBeforeAndAfterAncestryCommand()
    {
        $application = new Application($this->container);
        $application->setAutoExit(false);

        $applicationTester = new ApplicationTester($application);
        $applicationTester->run(array("command" => "testAncestry1", "--verbose" => 3));
        $output = $applicationTester->getDisplay();
        $this->assertRegExp("/Skip a before task testAncestry1 to prevent infinite loop. Because of existing it in ancestry tasks./", $output);

//        echo $output;
    }

}
