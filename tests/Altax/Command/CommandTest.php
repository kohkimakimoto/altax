<?php
namespace Test\Altax\Command;

use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\Console\Command\Command;
use Altax\Console\Application;
use Task;

class CommandTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
    }

    public function testCommand()
    {
    }
}

class Test001Command extends Command
{
    protected function fire()
    {
    }
}
