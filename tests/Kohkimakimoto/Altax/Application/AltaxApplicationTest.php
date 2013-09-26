<?php
namespace Test\Kohkimakimoto\Altax\Application;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Kohkimakimoto\Altax\Application\AltaxApplication;

class AltaxApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $application = new AltaxApplication();
        $application->setHomeConfigurationPath(__DIR__."/AltaxApplicationTest/.altax/home.php");
        $application->setDefaultConfigurationPath(__DIR__."/AltaxApplicationTest/.altax/default.php");
        $command = $application->find('config');

        $commandTester = new CommandTester($command);
        $commandTester->execute(
          array('command' => $command->getName()));
    }

}