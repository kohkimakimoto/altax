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
        $this->bufOutput = new BufferedOutput();
        $this->runtimeTask = new RuntimeTask(null, $this->task, $this->input, $this->bufOutput);

        ModuleFacade::clearResolvedInstances();
        ModuleFacade::setContainer($this->container);


        $module = new \Altax\Module\Server\ServerModule($this->container);
        $this->container->addModule(Server::getModuleName(), $module);

        $module = new \Altax\Module\Env\EnvModule($this->container);
        $this->container->addModule(Env::getModuleName(), $module);

        // Env::set('server.port', 2222);
        // Env::set("server.username", 'vagrant');
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

        $homedir = Env::get("homedir");
        $node = new Node();
        $node->setName("127.0.0.1");
        $process = new Process($this->runtimeTask, $node);
        $process->runLocally("echo helloworld", array("cwd" => $homedir));
        $output = $process->getRuntimeTask()->getOutput()->fetch();
        $this->assertRegExp("/helloworld/", $output);

        $os = php_uname('s');
        if(preg_match('/Windows/i', $os)){
            $regexp = '/Real command: cmd.exe \/C "cd ' . preg_quote($homedir, '/') . ' & echo helloworld"/';
        }else {
            $regexp = '/Real command: \/bin\/bash -l -c "cd ' . preg_quote($homedir, '/') . ' && echo helloworld"/';
        }
        $this->assertRegExp($regexp, $output);
    }

    public function testRunLocally2()
    {
        $this->runtimeTask->getOutput()->setVerbosity(3);

        $node = new Node();
        $node->setName("127.0.0.1");
        $process = new Process($this->runtimeTask, $node);

        $os = php_uname('s');
        if(preg_match('/Windows/i', $os)) {
            $process->runLocally(array(
                "cd C:/Windows/Temp",
                "cd",
            ));
            $output = $process->getRuntimeTask()->getOutput()->fetch();
            $this->assertRegExp('/Real command: cmd.exe \/C "cd C:\/Windows\/Temp & cd"/', $output);
        }else{
            $process->runLocally(array(
                "cd /var/tmp",
                "pwd",
            ));
            $output = $process->getRuntimeTask()->getOutput()->fetch();
            $this->assertRegExp('/Real command: \/bin\/bash -l -c "cd \/var\/tmp && pwd"/', $output);
        }
    }

    public function testGet()
    {
        // environment-dependent
        $srcPath = __DIR__ . "/ProcessTest/gettest.txt";

        $destPath = __DIR__."/../../../../tmp/Altax/Module/Task/Process/ProcessTest/gettest.txt";
        @unlink($destPath);

        $this->runtimeTask->getOutput()->setVerbosity(3);

        $node = new Node();
        $node->setName("127.0.0.1");
        $process = new Process($this->runtimeTask, $node);
        $process->get($srcPath, $destPath);
        $output = $process->getRuntimeTask()->getOutput()->fetch();

        $this->assertEquals(true, is_file($destPath));
        @unlink($destPath);
    }

    public function testGet2()
    {
        $srcPath = "/NotExistsFile/gettest.txt";
        $destPath = __DIR__."/../../../../tmp/Altax/Module/Task/Process/ProcessTest/gettest.txt";

        $this->runtimeTask->getOutput()->setVerbosity(3);

        $node = new Node();
        $node->setName("127.0.0.1");
        $process = new Process($this->runtimeTask, $node);

        try {
            $process->get($srcPath, $destPath);
            $this->assertEquals(false, true);
        } catch (\RuntimeException $e) {
            $this->assertEquals(true, true);
        }
        
    }

    public function testGetString()
    {
        // environment-dependent
        $srcPath = __DIR__."/ProcessTest/gettest.txt";

        $this->runtimeTask->getOutput()->setVerbosity(3);

        $node = new Node();
        $node->setName("127.0.0.1");
        $process = new Process($this->runtimeTask, $node);
        $retString =$process->getString($srcPath);
        $output = $process->getRuntimeTask()->getOutput()->fetch();
        $this->assertRegExp("/gettest contents[\r\n]*/", $retString);
    }

    public function testGetString2()
    {
        $srcPath = "/NotExistsFile/gettest.txt";

        $this->runtimeTask->getOutput()->setVerbosity(3);

        $node = new Node();
        $node->setName("127.0.0.1");
        $process = new Process($this->runtimeTask, $node);

        try {
            $process->getString($srcPath);
            $this->assertEquals(false, true);
        } catch (\RuntimeException $e) {
            $this->assertEquals(true, true);
        }
    }

    public function testPut()
    {
        $srcPath = __DIR__."/ProcessTest/puttest.txt";

        // environment-dependent
        $destPath = __DIR__."/../../../../tmp/Altax/Module/Task/Process/ProcessTest/puttest.txt";
        $destLocalPath = __DIR__."/../../../../tmp/Altax/Module/Task/Process/ProcessTest/puttest.txt";

        @unlink($destLocalPath);

        $this->runtimeTask->getOutput()->setVerbosity(3);

        $node = new Node();
        $node->setName("127.0.0.1");
        $process = new Process($this->runtimeTask, $node);
        $process->put($srcPath, $destPath);
        $output = $process->getRuntimeTask()->getOutput()->fetch();

        $this->assertEquals(true, is_file($destLocalPath));
        @unlink($destLocalPath);
    }

    public function testPut2()
    {
        $srcPath = "/NotExistsFile/gettest.txt";
        $destPath = __DIR__."/../../../../tmp/Altax/Module/Task/Process/ProcessTest/puttest.txt";

        $this->runtimeTask->getOutput()->setVerbosity(3);

        $node = new Node();
        $node->setName("127.0.0.1");
        $process = new Process($this->runtimeTask, $node);
        try {
            $process->put($srcPath, $destPath);
            $this->assertEquals(false, true);
        } catch (\RuntimeException $e) {
            $this->assertEquals(true, true);
        }
    }

    public function testPutString()
    {
        // environment-dependent
        $destPath = __DIR__."/../../../../tmp/Altax/Module/Task/Process/ProcessTest/puttest.txt";
        $destLocalPath = __DIR__."/../../../../tmp/Altax/Module/Task/Process/ProcessTest/puttest.txt";

        @unlink($destLocalPath);

        $this->runtimeTask->getOutput()->setVerbosity(3);

        $node = new Node();
        $node->setName("127.0.0.1");
        $process = new Process($this->runtimeTask, $node);
        $process->putString($destPath, "putstring contents");
        $output = $process->getRuntimeTask()->getOutput()->fetch();

        $this->assertEquals("putstring contents", file_get_contents($destLocalPath));
        @unlink($destLocalPath);
    }


    public function testPutString2()
    {
        $srcPath = "/NotExistsFile/gettest.txt";

        $this->runtimeTask->getOutput()->setVerbosity(3);

        $node = new Node();
        $node->setName("127.0.0.1");
        $process = new Process($this->runtimeTask, $node);
        try {
            $process->putString($srcPath, "putstring contents");
            $this->assertEquals(false, true);
        } catch (\RuntimeException $e) {
            $this->assertEquals(true, true);
        }
    }

}


