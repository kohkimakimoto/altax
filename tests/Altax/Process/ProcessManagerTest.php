<?php
namespace Test\Altax\Process;

use Altax\Process\ProcessManager;
use Symfony\Component\Console\Output\BufferedOutput;
use Altax\Server\Node;

class ProcessManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
        $this->app->instance("output", new BufferedOutput());
        $this->app["env"]->set('process.parallel', true);
    }

    public function testExecute()
    {
        $manager = new ProcessManager(
            function(){},
            $this->app['process.runtime'],
            $this->app['output'],
            $this->app['env']);

        $node1 = new Node(
            "localhost", 
            $this->app["key_passphrase_map"], 
            $this->app["env"]);

        $node2 = new Node(
            "127.0.0.1", 
            $this->app["key_passphrase_map"], 
            $this->app["env"]);

        $nodes = [$node1, $node2];
        $manager->executeWithNodes($nodes);


    }

    public function testExecuteSerial()
    {
        $this->app['env']->set('process.parallel', false);

        $manager = new ProcessManager(
            function(){

                $commandBuilder = $this->app["shell.command"];
                $command = $commandBuilder->make("pwd");
                $command->run();

            },
            $this->app['process.runtime'],
            $this->app['output'],
            $this->app['env']);

        $node1 = new Node(
            "localhost", 
            $this->app["key_passphrase_map"], 
            $this->app["env"]);

        $node2 = new Node(
            "127.0.0.1", 
            $this->app["key_passphrase_map"], 
            $this->app["env"]);

        $nodes = [$node1, $node2];
        $manager->executeWithNodes($nodes);

        $this->assertRegExp("/Run command: pwd/", $this->app['output']->fetch());
    }
}