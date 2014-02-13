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
    }

    public function testRun()
    {
        $this->runtimeTask->getOutput()->setVerbosity(3);

        $node = new Node();
        $node->setName("127.0.0.1");
        $process = new Process($this->runtimeTask, $node);
        $process->run("echo helloworld", array("cwd" => "~"));
        $output = $process->getRuntimeTask()->getOutput()->fetch();
        $this->assertRegExp("/helloworld/", $output);
        $this->assertRegExp('/Real command: \/bin\/bash -l -c "cd ~ && echo helloworld"/', $output);
    
        
        //echo $output;
    }

    public function testRunLocally()
    {
        $this->runtimeTask->getOutput()->setVerbosity(3);

        $node = new Node();
        $node->setName("127.0.0.1");
        $process = new Process($this->runtimeTask, $node);
        $process->runLocally("echo helloworld", array("cwd" => "~"));
        $output = $process->getRuntimeTask()->getOutput()->fetch();
        $this->assertRegExp("/helloworld/", $output);
        $this->assertRegExp('/Real command: \/bin\/bash -l -c "cd ~ && echo helloworld"/', $output);
    }

}

