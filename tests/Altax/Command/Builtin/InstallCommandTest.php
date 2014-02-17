<?php
namespace Test\Altax\Command\Builtin;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Tester\ApplicationTester;
use Altax\Command\Builtin\InstallCommand;
use Altax\Console\Application;
use Altax\Foundation\Container;

class InstallCommandTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->container = new Container();
    }

    public function testDefault()
    {
        /*
        $application = new Application($this->container);
        $application->setAutoExit(false);
        $application->add(new InstallCommand());
        $command = $application->find("install");

        $tmpDir = __DIR__."/../../../tmp/Altax/Command/Builtin/InstallCommandTest";
        @unlink($tmpDir);
        @mkdir($tmpDir."/.altax/", 0777, true);
        copy(__DIR__."/InstallCommandTest/.altax/composer.json", $tmpDir."/.altax/composer.json");

        $oldDir = getcwd();
        chdir($tmpDir);
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                "command" => $command->getName(),
                )
            );

        echo $commandTester->getDisplay();
        chdir($oldDir);

        @unlink($tmpDir);
        */
   }
}
