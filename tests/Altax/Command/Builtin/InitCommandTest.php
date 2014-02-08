<?php
namespace Test\Altax\Command\Builtin;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Tester\ApplicationTester;
use Altax\Command\Builtin\InitCommand;
use Altax\Console\Application;
use Altax\Foundation\Container;
use Altax\Module\Task\Resource\DefinedTask;

class InitCommandTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->container = new Container();
    }

    public function testDefault()
    {
        $application = new Application($this->container);
        $application->setAutoExit(false);
        $application->add(new InitCommand());
        $command = $application->find("init");

        $testTmpConfigPath = __DIR__."/../../../tmp/Altax/Command/Builtin/InitCommandTest/.altax/config.php";
        @unlink($testTmpConfigPath);
        @unlink(dirname($testTmpConfigPath)."/composer.json");

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                "command" => $command->getName(),
                "--path" => $testTmpConfigPath,
                )
            );

        $this->assertEquals(true, is_file($testTmpConfigPath));
        $this->assertEquals(true, is_file(dirname($testTmpConfigPath)."/composer.json"));

        unlink($testTmpConfigPath);
        unlink(dirname($testTmpConfigPath)."/composer.json");
    }
}
