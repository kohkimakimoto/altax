<?php
namespace Test\Altax\Command\Builtin;

use Symfony\Component\Console\Tester\CommandTester;
use Altax\Command\Builtin\InitCommand;
use Altax\Console\Application;

class InitCommandTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
    }

    public function testCommand()
    {
        $application = new Application($this->app);
        $application->setAutoExit(false);
        $application->add(new InitCommand());
        $command = $application->find("init");

        $testTmpConfigPath = __DIR__."/../../../tmp/Altax/Command/Builtin/InitCommandTest/.altax/config.php";
        @mkdir(dirname(dirname($testTmpConfigPath)), 0777, true);

        @unlink($testTmpConfigPath);
        @unlink(dirname($testTmpConfigPath)."/composer.json");

        $orgDir = getcwd();

        chdir(dirname(dirname($testTmpConfigPath)));

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                "command" => $command->getName(),
                )
            );

        // Only checks to exist files. Doesn't inspect content of files.
        $this->assertEquals(true, is_file($testTmpConfigPath));
        $this->assertEquals(true, is_file(dirname($testTmpConfigPath)."/composer.json"));

        // One more test. When files exits. it echo skip message.
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                "command" => $command->getName(),
                )
            );
        $this->assertRegExp("/Skiped creation process/", $commandTester->getDisplay());

        unlink($testTmpConfigPath);
        unlink(dirname($testTmpConfigPath)."/composer.json");
        chdir($orgDir);
    }
}
