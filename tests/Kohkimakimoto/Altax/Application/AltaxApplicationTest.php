<?php
namespace Test\Kohkimakimoto\Altax\Application;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Kohkimakimoto\Altax\Application\AltaxApplication;

class AltaxApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function testExecuteConfig()
    {
        $application = new AltaxApplication();
        $application->setHomeConfigurationPath(__DIR__."/AltaxApplicationTest/.altax/home.php");
        $application->setDefaultConfigurationPath(__DIR__."/AltaxApplicationTest/.altax/default.php");
        $command = $application->find('config');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $output = $commandTester->getDisplay();
    }

    public function testExecuteInit()
    {
        $application = new AltaxApplication();
        $application->setHomeConfigurationPath(__DIR__."/AltaxApplicationTest/.altax/home.php");
        $application->setDefaultConfigurationPath(__DIR__."/AltaxApplicationTest/.altax/default.php");
        $command = $application->find('init');

        if (file_exists( __DIR__."/../../../../tmp/.altax/config.php")) {
            unlink( __DIR__."/../../../../tmp/.altax/config.php");
        }

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName(), "--path" => __DIR__."/../../../../tmp/.altax/config.php"));

        $this->assertEquals(true, file_exists(__DIR__."/../../../../tmp/.altax/config.php"));
    }
}