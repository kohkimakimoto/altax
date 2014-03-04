<?php
namespace Test\Altax\Command\Builtin;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Tester\ApplicationTester;
use Altax\Command\Builtin\RequireCommand;
use Altax\Console\Application;
use Altax\Foundation\Container;
use Altax\Module\Task\Resource\DefinedTask;

class RequireCommandTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->container = new Container();
    }

    public function testDefault()
    {
        $application = new Application($this->container);
        $application->setAutoExit(false);
        $application->add(new RequireCommand());
        $command = $application->find("require");

        $testTmpConfigDir = __DIR__."/../../../tmp/Altax/Command/Builtin/RequireCommandTest/.altax";
        @mkdir($testTmpConfigDir, 0777, true);
        @copy(__DIR__."/RequireCommandTest/.altax/composer.json", $testTmpConfigDir."/composer.json");

        $orgDir = getcwd();

        chdir(dirname($testTmpConfigDir));

        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                "command" => $command->getName(),
                "packages" => array("foo/bar:1.0.0"),
                "--no-update" => true,
                )
            );

        $composerJson = file_get_contents($testTmpConfigDir."/composer.json");
        $expected = <<<EOL
{
    "require": {
        "foo/bar": "1.0.0"
    }
}

EOL;
        $this->assertSame($expected, $composerJson);
        chdir($orgDir);
        @unlink($testTmpConfigDir."/composer.json");
    }
}
