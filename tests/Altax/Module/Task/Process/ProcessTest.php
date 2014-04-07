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
use Altax\Module\Env\Facade\Env;


class ProcessTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->container = new Container();

        $this->task = new DefinedTask();
        $this->task->setName("test_process_run");
        $this->input = new ArgvInput();
        $this->output = new BufferedOutput();
        $this->runtimeTask = new RuntimeTask(null, $this->task, $this->input, $this->output);

        ModuleFacade::clearResolvedInstances();
        ModuleFacade::setContainer($this->container);


        $module = new \Altax\Module\Server\ServerModule($this->container);
        $this->container->addModule(Server::getModuleName(), $module);

        $module = new \Altax\Module\Env\EnvModule($this->container);
        $this->container->addModule(Env::getModuleName(), $module);
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

    public function testRunLocally1()
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

    public function testRunLocally2()
    {
        $this->runtimeTask->getOutput()->setVerbosity(3);

        $node = new Node();
        $node->setName("127.0.0.1");
        $process = new Process($this->runtimeTask, $node);
        $process->runLocally(array(
            "cd /var/tmp",
            "pwd",
        ));
        $output = $process->getRuntimeTask()->getOutput()->fetch();
        $this->assertRegExp('/Real command: \/bin\/bash -l -c "cd \/var\/tmp && pwd"/', $output);
    }

    public function testGet()
    {
        @unlink(__DIR__."/../../../../tmp/Altax/Module/Task/Process/ProcessTest/gettest.txt");

        $this->runtimeTask->getOutput()->setVerbosity(3);

        $node = new Node();
        $node->setName("127.0.0.1");
        $process = new Process($this->runtimeTask, $node);
        $process->get(__DIR__."/ProcessTest/gettest.txt", __DIR__."/../../../../tmp/Altax/Module/Task/Process/ProcessTest/gettest.txt");
        $output = $process->getRuntimeTask()->getOutput()->fetch();

        $this->assertEquals(true, is_file(__DIR__."/../../../../tmp/Altax/Module/Task/Process/ProcessTest/gettest.txt"));
        @unlink(__DIR__."/../../../../tmp/Altax/Module/Task/Process/ProcessTest/gettest.txt");
    }

    public function testGet2()
    {
        $this->runtimeTask->getOutput()->setVerbosity(3);

        $node = new Node();
        $node->setName("127.0.0.1");
        $process = new Process($this->runtimeTask, $node);

        try {
            $process->get("/NotExistsFile/gettest.txt", __DIR__."/../../../../tmp/Altax/Module/Task/Process/ProcessTest/gettest.txt");
            $this->assertEquals(false, true);
        } catch (\RuntimeException $e) {
            $this->assertEquals(true, true);
        }
        
    }

    public function testGetString()
    {
        @unlink(__DIR__."/../../../../tmp/Altax/Module/Task/Process/ProcessTest/gettest.txt");

        $this->runtimeTask->getOutput()->setVerbosity(3);

        $node = new Node();
        $node->setName("127.0.0.1");
        $process = new Process($this->runtimeTask, $node);
        $retString =$process->getString(__DIR__."/ProcessTest/gettest.txt");
        $output = $process->getRuntimeTask()->getOutput()->fetch();

        $this->assertEquals("gettest contents", $retString);

        @unlink(__DIR__."/../../../../tmp/Altax/Module/Task/Process/ProcessTest/gettest.txt");
    }

    public function testGetString2()
    {
        $this->runtimeTask->getOutput()->setVerbosity(3);

        $node = new Node();
        $node->setName("127.0.0.1");
        $process = new Process($this->runtimeTask, $node);

        try {
            $process->getString("/NotExistsFile/gettest.txt");
            $this->assertEquals(false, true);
        } catch (\RuntimeException $e) {
            $this->assertEquals(true, true);
        }
    }

    public function testPut()
    {
        @unlink(__DIR__."/../../../../tmp/Altax/Module/Task/Process/ProcessTest/gettest.txt");

        $this->runtimeTask->getOutput()->setVerbosity(3);

        $node = new Node();
        $node->setName("127.0.0.1");
        $process = new Process($this->runtimeTask, $node);
        $process->put(__DIR__."/ProcessTest/puttest.txt", __DIR__."/../../../../tmp/Altax/Module/Task/Process/ProcessTest/puttest.txt");
        $output = $process->getRuntimeTask()->getOutput()->fetch();

        $this->assertEquals(true, is_file(__DIR__."/../../../../tmp/Altax/Module/Task/Process/ProcessTest/puttest.txt"));
        @unlink(__DIR__."/../../../../tmp/Altax/Module/Task/Process/ProcessTest/puttest.txt");
    }

    public function testPut2()
    {
        @unlink(__DIR__."/../../../../tmp/Altax/Module/Task/Process/ProcessTest/gettest.txt");

        $this->runtimeTask->getOutput()->setVerbosity(3);

        $node = new Node();
        $node->setName("127.0.0.1");
        $process = new Process($this->runtimeTask, $node);
        try {
            $process->put("/NotExistsFile/gettest.txt", __DIR__."/../../../../tmp/Altax/Module/Task/Process/ProcessTest/puttest.txt");
            $this->assertEquals(false, true);
        } catch (\RuntimeException $e) {
            $this->assertEquals(true, true);
        }
    }

    public function testPutString()
    {
        @unlink(__DIR__."/../../../../tmp/Altax/Module/Task/Process/ProcessTest/gettest.txt");

        $this->runtimeTask->getOutput()->setVerbosity(3);

        $node = new Node();
        $node->setName("127.0.0.1");
        $process = new Process($this->runtimeTask, $node);
        $process->putString(__DIR__."/../../../../tmp/Altax/Module/Task/Process/ProcessTest/puttest.txt", "putstring contents");
        $output = $process->getRuntimeTask()->getOutput()->fetch();

        $this->assertEquals("putstring contents", file_get_contents(__DIR__."/../../../../tmp/Altax/Module/Task/Process/ProcessTest/puttest.txt"));
        @unlink(__DIR__."/../../../../tmp/Altax/Module/Task/Process/ProcessTest/puttest.txt");
    }


    public function testPutString2()
    {
        @unlink(__DIR__."/../../../../tmp/Altax/Module/Task/Process/ProcessTest/gettest.txt");

        $this->runtimeTask->getOutput()->setVerbosity(3);

        $node = new Node();
        $node->setName("127.0.0.1");
        $process = new Process($this->runtimeTask, $node);
        try {
            $process->putString("/NotExistsFile/gettest.txt", "putstring contents");
            $this->assertEquals(false, true);
        } catch (\RuntimeException $e) {
            $this->assertEquals(true, true);
        }
    }

}


