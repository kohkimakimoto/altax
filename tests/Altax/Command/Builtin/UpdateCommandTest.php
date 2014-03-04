<?php
namespace Test\Altax\Command\Builtin;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Tester\ApplicationTester;
use Altax\Command\Builtin\UpdateCommand;
use Altax\Console\Application;
use Altax\Foundation\Container;

class UpdateCommandTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->container = new Container();
    }

    public function testDefault()
    {
        $application = new Application($this->container);
        $application->setAutoExit(false);
        $application->add(new UpdateCommand());
        $command = $application->find("update");

        $testTmpConfigDir = __DIR__."/../../../tmp/Altax/Command/Builtin/UpdateCommandTest/.altax";
        @mkdir($testTmpConfigDir, 0777, true);
        @copy(__DIR__."/UpdateCommandTest/.altax/composer.json", $testTmpConfigDir."/composer.json");

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
Updating dependencies (including require-dev)
Nothing to install or update

EOL;
        $this->assertSame($expected, $commandTester->getDisplay());
        chdir($orgDir);
        @unlink($testTmpConfigDir."/composer.json");
   }
}
