<?php
namespace Test\Kohkimakimoto\Altax\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

use Kohkimakimoto\Altax\Application\AltaxApplication;
use Kohkimakimoto\Altax\Command\InitCommand;


class InitCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        /*
        $application = new AltaxApplication();
        $application->setHomeConfigurationPath(null);
        $application->setDefaultConfigurationPath(null);

        $command = $application->find('init');

        $path = tempnam(sys_get_temp_dir(), "InitCommandTest");
        unlink($path);

        $commandTester = new CommandTester($command);
        $commandTester->execute(
          array('command' => $command->getName(), '--path' => $path));
        $this->assertEquals(true, is_file($path));
        unlink($path);
        */
    }
}