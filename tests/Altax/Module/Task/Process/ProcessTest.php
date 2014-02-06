<?php
namespace Test\Altax\Module\Task\Process;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;

use Altax\Foundation\Container;
use Altax\Foundation\ModuleFacade;
use Altax\Module\Task\Resource\DefinedTask;
use Altax\Module\Task\Resource\RuntimeTask;
use Altax\Module\Task\Process\Process;

class ProcessTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->container = new Container();

        ModuleFacade::clearResolvedInstances();
        ModuleFacade::setContainer($this->container);
    }

    public function testProcessRun()
    {
        $task = new DefinedTask();
        $task->setName("test_process_run");

        $input = new ArgvInput();
        $output = new BufferedOutput();

        $runtimeTask = new RuntimeTask($task, $input, $output);
        $process = new Process($runtimeTask);
        $process->setCommandline("echo testecho");
        $process->setTimeout(null);
        $process->run();

        $this->assertEquals("testecho\n", $output->fetch());
    }

}