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

    public function testBuiltinTaskConfig()
    {
        $application = new AltaxApplication();
        $application->setHomeConfigurationPath(__DIR__."/AltaxApplicationTest/.altax/home.php");
        $application->setDefaultConfigurationPath(__DIR__."/AltaxApplicationTest/.altax/default.php");
        $application->setAutoExit(false);

        $applicationTester = new ApplicationTester($application);
        $applicationTester->run(array('command' => "config", "--path" => __DIR__."/../../../../tmp/.altax/config.php"));
    }

    public function testBuiltinTaskInit()
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

        $this->assertEquals(true, file_exists(__DIR__."/../../../../tmp/.altax/config.php"));
    }

    public function testSampleTask()
    {
        $application = new AltaxApplication();
        $application->setHomeConfigurationPath(__DIR__."/AltaxApplicationTest/.altax/home.php");
        $application->setDefaultConfigurationPath(__DIR__."/AltaxApplicationTest/.altax/default.php");
        $application->setAutoExit(false);

        $applicationTester = new ApplicationTester($application);
        $applicationTester->run(array('command' => "sample", "--debug" => true));

        $applicationTester->getDisplay();
    }



}