<?php
namespace Test\Altax\Module\Task\Process;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;

use Altax\Foundation\Container;
use Altax\Foundation\ModuleFacade;
use Altax\Module\Task\Resource\DefinedTask;
use Altax\Module\Task\Resource\RuntimeTask;
use Altax\Module\Task\Process\Process;
use Altax\Module\Server\Resource\Node;


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

    public function testCompileCommandline()
    {
        $task = new DefinedTask();
        $task->setName("test_process_run");

        $input = new ArgvInput();
        $output = new BufferedOutput();

        $node = new Node();
        $node->setName("nameeeee!");

        $runtimeTask = new RuntimeTask($task, $input, $output);
        $process = new Process($runtimeTask);
        $ret = $process->compileCommandline('echo {{ $node->getName() }}', $node);
        $this->assertEquals("echo nameeeee!", $ret);
    }

}


