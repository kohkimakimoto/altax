<?php
namespace Test\Altax\Command\Builtin;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Tester\ApplicationTester;
use Altax\Command\Builtin\InstallCommand;
use Altax\Console\Application;

class InstallCommandTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->app = bootAltaxApplication();
    }

    public function testDefault()
    {
        $application = new Application($this->app);
        $application->setAutoExit(false);
        $application->add(new InstallCommand());
        $command = $application->find("install");

        $testTmpConfigDir = __DIR__."/../../../tmp/Altax/Command/Builtin/InstallCommandTest/.altax";
        @mkdir($testTmpConfigDir, 0777, true);
        @copy(__DIR__."/InstallCommandTest/composer.json", $testTmpConfigDir."/composer.json");

        $orgDir = getcwd();
        chdir(dirname($testTmpConfigDir));

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                "command" => $command->getName(),
                "--dry-run" => true,
                )
            );

        $expected = <<<EOL
Loading composer repositories with package information
Installing dependencies (including require-dev)
Nothing to install or update

EOL;
        $this->assertSame($expected, $commandTester->getDisplay());
        chdir($orgDir);
        @unlink($testTmpConfigDir."/composer.json");
   }
}
