<?php
namespace Test\Kohkimakimoto\Altax\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

use Kohkimakimoto\Altax\Application\AltaxApplication;
use Kohkimakimoto\Altax\Command\BaseCommand;

class BaseCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $application = new AltaxApplication();
        $application->setHomeConfigurationPath(__DIR__."/BaseCommandTest/.altax/home.php");
        $application->setDefaultConfigurationPath(__DIR__."/BaseCommandTest/.altax/default.php");
        $command = $application->find('config');

        $commandTester = new CommandTester($command);
        $commandTester->execute(
          array('command' => $command->getName()));
    }
}