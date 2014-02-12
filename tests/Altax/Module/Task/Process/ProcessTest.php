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
use Altax\Module\Server\Facade\Server;
use Altax\Module\Task\Facade\Task;

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

        $this->container->addModule(
            \Altax\Module\Server\Facade\Server::getModuleName(),
            new \Altax\Module\Server\ServerModule($this->container)
            );
        $this->container->addModule(
            \Altax\Module\Task\Facade\Task::getModuleName(),
            new \Altax\Module\Task\TaskModule($this->container)
            );

        Server::node("127.0.0.1", "test");
        Server::node("localhost", "test");

        Server::node("nodeIsSameNameOfRole", "nodeIsSameNameOfRole");
        
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

    public function testTo1()
    {
        $process = new Process($this->runtimeTask);

        // In order to check output debug message.
        $this->runtimeTask->getOutput()->setVerbosity(2);

        try {
            $process->to();
            $this->assertEquals(false, true);
        } catch (\RuntimeException $e) {
            $this->assertEquals(true, true);
        }
    }

    public function testTo2()
    {
        $process = new Process($this->runtimeTask);

        // In order to check output debug message.
        $this->runtimeTask->getOutput()->setVerbosity(2);
        
        $process->to("127.0.0.1");

        $this->assertEquals("Process#to set 1 nodes: 127.0.0.1\n", $this->runtimeTask->getOutput()->fetch());
    }

    public function testTo3()
    {
        $process = new Process($this->runtimeTask);

        // In order to check output debug message.
        $this->runtimeTask->getOutput()->setVerbosity(2);
        
        $process->to("127.0.0.1", "localhost");

        $this->assertEquals("Process#to set 2 nodes: 127.0.0.1, localhost\n", $this->runtimeTask->getOutput()->fetch());
    }

    public function testTo4()
    {
        $process = new Process($this->runtimeTask);

        // In order to check output debug message.
        $this->runtimeTask->getOutput()->setVerbosity(2);
        
        $process->to(array("127.0.0.1", "localhost"));

        $this->assertEquals("Process#to set 2 nodes: 127.0.0.1, localhost\n", $this->runtimeTask->getOutput()->fetch());
    }

    public function testTo5()
    {
        $process = new Process($this->runtimeTask);

        // In order to check output debug message.
        $this->runtimeTask->getOutput()->setVerbosity(2);
        
        $process->to(array("test"));

        $this->assertEquals("Process#to set 2 nodes: 127.0.0.1, localhost\n", $this->runtimeTask->getOutput()->fetch());
    }


    public function testTo6()
    {
        $process = new Process($this->runtimeTask);

        // In order to check output debug message.
        $this->runtimeTask->getOutput()->setVerbosity(2);
        
        $process->to(array("roles" => "test"));

        $this->assertEquals("Process#to set 2 nodes: 127.0.0.1, localhost\n", $this->runtimeTask->getOutput()->fetch());
    }

    public function testTo7()
    {
        $process = new Process($this->runtimeTask);

        // In order to check output debug message.
        $this->runtimeTask->getOutput()->setVerbosity(2);
        
        $process->to(array("nodes" => array("127.0.0.1", "localhost")));

        $this->assertEquals("Process#to set 2 nodes: 127.0.0.1, localhost\n", $this->runtimeTask->getOutput()->fetch());
    }

    public function testTo8()
    {
        $process = new Process($this->runtimeTask);

        // In order to check output debug message.
        $this->runtimeTask->getOutput()->setVerbosity(2);
        
        $process->to(array("roles" => array("test")));

        $this->assertEquals("Process#to set 2 nodes: 127.0.0.1, localhost\n", $this->runtimeTask->getOutput()->fetch());
    }

    public function testTo9()
    {
        $process = new Process($this->runtimeTask);

        // In order to check output debug message.
        $this->runtimeTask->getOutput()->setVerbosity(2);
        
        $process->to("unknown.sample.com");

        $this->assertEquals("Process#to set 1 nodes: unknown.sample.com\n", $this->runtimeTask->getOutput()->fetch());
    }

    public function testTo10()
    {
        $process = new Process($this->runtimeTask);

        // In order to check output debug message.
        $this->runtimeTask->getOutput()->setVerbosity(2);
        
        try {
            $process->to("nodeIsSameNameOfRole");
            $this->assertEquals(false, true);
        } catch (\RuntimeException $e) {
            $this->assertEquals(true, true);
        }

    }


    public function testUserAndCwd()
    {
        $process = new Process($this->runtimeTask);

        // In order to check output debug message.
        $this->runtimeTask->getOutput()->setVerbosity(2);
        
        $process->user("root")->cwd("/tmp/");
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

    public function testRun2()
    {
        $process = new Process($this->runtimeTask);
        $process->setCommandline("echo testecho");
        $process->to("127.0.0.1")->run();
    }

    public function testCompileCommandline()
    {
        $process = new Process($this->runtimeTask);

        $node = new Node();
        $node->setName("nameeeee!");

        $ret = $process->compileCommandline('echo {{ $node->getName() }}', $node);
        $this->assertEquals("echo nameeeee!", $ret);
    }

    public function testCompileRealCommand()
    {
        $process = new Process($this->runtimeTask);

        $node = new Node();
        $ret = $process->compileRealCommand("ls -la");
        $this->assertEquals('/bin/bash -l -c "ls -la"', $ret);

        $process = new Process($this->runtimeTask);

        $node = new Node();
        $process->user("kohkimakimoto");
        $ret = $process->compileRealCommand("ls -la");
        $this->assertEquals('sudo -ukohkimakimoto TERM=dumb /bin/bash -l -c "ls -la"', $ret);

        $node = new Node();
        $process->user("kohkimakimoto");
        $process->cwd("/var/tmp");
        $ret = $process->compileRealCommand("ls -la");
        $this->assertEquals('sudo -ukohkimakimoto TERM=dumb /bin/bash -l -c "cd /var/tmp && ls -la"', $ret);
    }
}


