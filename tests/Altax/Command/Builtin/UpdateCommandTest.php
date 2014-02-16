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

        $commandTester = new CommandTester($command);
        /*
        $commandTester->execute(
            array(
                "command" => $command->getName(),
                )
            );

        // echo $commandTester->getDisplay();
        */
   }
}
