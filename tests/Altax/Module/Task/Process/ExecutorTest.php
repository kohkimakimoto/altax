<?php
namespace Test\Altax\Module\Task\Process;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;

use Altax\Foundation\Container;
use Altax\Foundation\ModuleFacade;
use Altax\Module\Task\Resource\DefinedTask;
use Altax\Module\Task\Resource\RuntimeTask;
use Altax\Module\Server\Resource\Node;
use Altax\Module\Server\Facade\Server;
use Altax\Module\Task\Facade\Task;
use Altax\Module\Task\Process\Executor;

class ExecutorTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->container = new Container();
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
        $this->container->addModule(
            \Altax\Module\Env\Facade\Env::getModuleName(),
            new \Altax\Module\Env\EnvModule($this->container)
            );

        Server::node("127.0.0.1", "test");
        Server::node("localhost", "test");
        Server::node("nodeIsSameNameOfRole", "nodeIsSameNameOfRole");

        $this->task = new DefinedTask();
        $this->task->setName("test_process_run");
        $this->input = new ArgvInput();
        $this->output = new BufferedOutput();
        $this->runtimeTask = new RuntimeTask(null, $this->task, $this->input, $this->output);
    }

    public function testExecute1()
    {
        $this->output->setVerbosity(3);

        $executor = new Executor($this->runtimeTask, function(){}, array());
        $executor->execute();

        $output = $this->output->fetch();
        $this->assertRegExp("/Found 0 nodes/", $output);
    }

    public function testExecute2()
    {
        $this->output->setVerbosity(3);

        $executor = new Executor($this->runtimeTask, 
            function($process){



            }, 
            array("127.0,0,1"));
        $executor->execute();

        $output = $this->output->fetch();
        $this->assertRegExp("/Found 1 nodes/", $output);
    }

    public function testExecute3()
    {
        $this->output->setVerbosity(3);

        $executor = new Executor($this->runtimeTask, 
            function($process){



            }, 
            array("127.0,0,1", "localhost"));
        $executor->execute();

        $output = $this->output->fetch();
        $this->assertRegExp("/Found 2 nodes/", $output);
    }

    public function testExecute4()
    {
        $this->output->setVerbosity(3);

        try {
            $executor = new Executor($this->runtimeTask, 
                function($process){
                }, 
                array("nodeIsSameNameOfRole"));
            $this->assertEquals(false, true);
        } catch (\RuntimeException $e) {
            $this->assertEquals(true, true);
        }

    }

    public function testExecute5()
    {
        $this->output->setVerbosity(3);

        $executor = new Executor($this->runtimeTask, 
            function($process){



            }, 
            array("test"));
        $executor->execute();

        $output = $this->output->fetch();
        $this->assertRegExp("/Found 2 nodes/", $output);
    }

    public function testExecute6()
    {
        // role array
        $this->output->setVerbosity(3);

        $executor = new Executor($this->runtimeTask, 
            function($process){



            }, 
            array("roles" => array("test")));
        $executor->execute();

        // role string
        $output = $this->output->fetch();
        $this->assertRegExp("/Found 2 nodes/", $output);

        $this->output->setVerbosity(3);

        $executor = new Executor($this->runtimeTask, 
            function($process){



            }, 
            array("roles" => "test"));
        $executor->execute();

        $output = $this->output->fetch();
        $this->assertRegExp("/Found 2 nodes/", $output);
    }

    public function testExecute7()
    {
        // nodes array
        $this->output->setVerbosity(3);

        $executor = new Executor($this->runtimeTask, 
            function($process){



            }, 
            array("nodes" => array("127.0.0.1")));
        $executor->execute();

        // nodes string
        $output = $this->output->fetch();
        $this->assertRegExp("/Found 1 nodes/", $output);

        $this->output->setVerbosity(3);

        $executor = new Executor($this->runtimeTask, 
            function($process){



            }, 
            array("nodes" => "127.0.0.1"));
        $executor->execute();

        $output = $this->output->fetch();
        $this->assertRegExp("/Found 1 nodes/", $output);
    }

    public function testExecuteInSerial()
    {
        // nodes array
        $this->output->setVerbosity(3);

        $executedNodes = array();

        $executor = new Executor($this->runtimeTask, 
            function($process) use (&$executedNodes) {
                $executedNodes[] = $process->getNodeName();
            }, 
            array("nodes" => array("127.0.0.1", "localhost")));
        $executor->setIsParallel(false);
        $this->assertEquals(false, $executor->getIsParallel());
        $executor->execute();

        // nodes string
        $output = $this->output->fetch();
        $this->assertRegExp("/Running serial mode/", $output);

        $this->assertCount(2, $executedNodes);
        $this->assertContains("127.0.0.1", $executedNodes);
        $this->assertContains("localhost", $executedNodes);
    }

}
