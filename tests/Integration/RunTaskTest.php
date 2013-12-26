<?php
namespace Test\Integration;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Output\ConsoleOutput;

use Symfony\Component\Process\Process;

use Kohkimakimoto\Altax\Util\Context;
use Kohkimakimoto\Altax\Application\AltaxApplication;

class RunTaskTest extends \PHPUnit_Framework_TestCase
{

    public function testRunTaskBeforeafter()
    {
        $rootDir = realpath(__DIR__."/../..");
        $configFile = realpath(__DIR__."/.altax/config.php");

        $process = new Process("php $rootDir/bin/altax -f=$configFile test002");
        $process->run();
        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }
        
        $this->assertRegExp("/Executing task test001/", $process->getOutput());
        $this->assertRegExp("/Executing task test002/", $process->getOutput());
        $this->assertRegExp("/Executing task test003/", $process->getOutput());
    }

    public function testRunTaskLocalrun()
    {
        $rootDir = realpath(__DIR__."/../..");
        $configFile = realpath(__DIR__."/.altax/config.php");

        $process = new Process("php $rootDir/bin/altax -f=$configFile localrun_test001 -vvv");
        $process->run();
        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }
        
        $this->assertRegExp("/Debug: Running at the localhost only/", $process->getOutput());

    }
}