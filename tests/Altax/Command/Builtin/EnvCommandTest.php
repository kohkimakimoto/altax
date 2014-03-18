<?php
namespace Test\Altax\Command\Builtin;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Tester\ApplicationTester;
use Altax\Command\Builtin\EnvCommand;
use Altax\Console\Application;
use Altax\Foundation\Container;
use Altax\Foundation\ModuleFacade;
use Altax\Module\Env\Facade\Env;

class EnvCommandTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->container = new Container();

        ModuleFacade::clearResolvedInstances();
        ModuleFacade::setContainer($this->container);

        $module = new \Altax\Module\Env\EnvModule($this->container);
        $this->container->addModule(Env::getModuleName(), $module);
    }

    public function testDefault()
    {
        $application = new Application($this->container);
        $application->setAutoExit(false);
        $application->add(new EnvCommand());
        $command = $application->find("env");
        
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            array(
                "command" => $command->getName(),
                )
            );
        /*
        $expected = <<<EOL
EOL;
        $this->assertEquals($expected, $commandTester->getDisplay());
        */

    }

}
