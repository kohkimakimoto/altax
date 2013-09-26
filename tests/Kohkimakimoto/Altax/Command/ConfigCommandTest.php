<?php
namespace Test\Kohkimakimoto\Altax\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

use Kohkimakimoto\Altax\Application\AltaxApplication;
use Kohkimakimoto\Altax\Command\ConfigCommand;

class ConfigCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $application = new AltaxApplication();
        $application->setHomeConfigurationPath(null);
        $application->setDefaultConfigurationPath(null);

        $command = $application->find('config');

        $path = __DIR__."/ConfigCommandTest/.altax/altax.php";

        $commandTester = new CommandTester($command);
        $commandTester->execute(
          array('command' => $command->getName(), '--file' => $path));
        
        $expectedContents = <<<EOL
Defined configurations
  tasks/sample/desc => This is a sample task.
  tasks/sample/callback => function()
  tasks/sample/options/roles => web
  hosts => array()
  roles/web/0 => 127.0.0.1

EOL;

        $this->assertEquals($expectedContents, $commandTester->getDisplay());
    }
}