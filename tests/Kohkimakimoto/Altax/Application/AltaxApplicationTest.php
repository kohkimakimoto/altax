<?php
namespace Test\Kohkimakimoto\Altax\Application;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Tester\ApplicationTester;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Output\ConsoleOutput;


use Kohkimakimoto\Altax\Util\Context;
use Kohkimakimoto\Altax\Application\AltaxApplication;


class AltaxApplicationTest extends \PHPUnit_Framework_TestCase
{

    public function testBuiltinCommandConfig()
    {
        $application = new AltaxApplication();
        $application->setHomeConfigurationPath(__DIR__."/AltaxApplicationTest/.altax/home.php");
        $application->setDefaultConfigurationPath(__DIR__."/AltaxApplicationTest/.altax/default.php");
        $application->setAutoExit(false);

        $applicationTester = new ApplicationTester($application);
        $applicationTester->run(array('command' => "config"));

        $output = $applicationTester->getDisplay();
    }

    public function testBuiltinCommandConfig2()
    {
        $application = new AltaxApplication();
        $application->setHomeConfigurationPath(__DIR__."/AltaxApplicationTest/.altax/home.php");
        $application->setDefaultConfigurationPath(__DIR__."/AltaxApplicationTest/.altax/default.php");
        $application->setAutoExit(false);

        $applicationTester = new ApplicationTester($application);
        $applicationTester->run(array('command' => "config", "--file" => __DIR__."/AltaxApplicationTest/.altax/file.php"));

        $output = $applicationTester->getDisplay();
    }

    public function testBuiltinCommandInit()
    {
        $application = new AltaxApplication();
        
        $application->setHomeConfigurationPath(__DIR__."/AltaxApplicationTest/.altax/home.php");
        $application->setDefaultConfigurationPath(__DIR__."/AltaxApplicationTest/.altax/default.php");
        $application->setAutoExit(false);
        
        if (file_exists( __DIR__."/../../../../tmp/.altax/config.php")) {
            unlink( __DIR__."/../../../../tmp/.altax/config.php");
        }

        $applicationTester = new ApplicationTester($application);
        $applicationTester->run(array('command' => "init", "--path" => __DIR__."/../../../../tmp/.altax/config.php"));

        $output = $applicationTester->getDisplay();

        $this->assertEquals(true, file_exists(__DIR__."/../../../../tmp/.altax/config.php"));
    }

    public function testTaskCommandSample()
    {
        $application = new AltaxApplication();
        $application->setHomeConfigurationPath(__DIR__."/AltaxApplicationTest/.altax/home.php");
        $application->setDefaultConfigurationPath(__DIR__."/AltaxApplicationTest/.altax/default.php");
        $application->setAutoExit(false);

        $applicationTester = new ApplicationTester($application);
        $applicationTester->run(array('command' => "sample", "--debug" => true));

        $output = $applicationTester->getDisplay();
    }



}