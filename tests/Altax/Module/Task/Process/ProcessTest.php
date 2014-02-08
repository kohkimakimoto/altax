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
        $this->task = new DefinedTask();
        $this->task->setName("test_process_run");
        $this->input = new ArgvInput();
        $this->output = new BufferedOutput();
        $this->runtimeTask = new RuntimeTask($this->task, $this->input, $this->output);

        ModuleFacade::clearResolvedInstances();
        ModuleFacade::setContainer($this->container);
    }

    public function testAccessorOfCommandline()
    {
        $process = new Process($this->runtimeTask);
        $process->setCommandline("ls -la");
        $this->assertEquals("ls -la", $process->getCommandline());
    }

    public function testAccessorOfClosure()
    {
        $process = new Process($this->runtimeTask);
        $closure = function(){};
        $process->setClosure($closure);
        $this->assertSame($closure, $process->getClosure());
        $this->assertEquals(true, $process->hasClosure());
    }

    public function testAccessorOfTimeout()
    {
        $process = new Process($this->runtimeTask);
        $process->setTimeout(1234);
        $this->assertEquals(1234, $process->getTimeout());
    }

    public function testGetRuntimeTask()
    {
        $process = new Process($this->runtimeTask);
        $this->assertEquals($this->runtimeTask, $process->getRuntimeTask());        
    }

    public function testRunLocally()
    {
        $process = new Process($this->runtimeTask);
        $process->setCommandline("echo testecho");
        $process->runLocally();

        $this->assertEquals("testecho\n", $process->getRuntimeTask()->getOutput()->fetch());
    }

    public function testRun()
    {
        $process = new Process($this->runtimeTask);
        $process->setCommandline("echo testecho");

        try {
            $process->run();
            $this->assertEquals(false, true);
        } catch (\RuntimeException $e) {
            // Not found any remote node to connect.
            $this->assertEquals(true, true);
        }
    }

    public function testCompileCommandline()
    {
        $process = new Process($this->runtimeTask);

        $node = new Node();
        $node->setName("nameeeee!");

        $ret = $process->compileCommandline('echo {{ $node->getName() }}', $node);
        $this->assertEquals("echo nameeeee!", $ret);
    }

}


