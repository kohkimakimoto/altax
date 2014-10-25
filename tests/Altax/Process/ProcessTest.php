<?php
namespace Test\Altax\Process;

use Altax\Process\Process;
use Altax\Server\Node;

class ProcessTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
    }

    public function testToString()
    {
        $node = new Node("localhost", $this->app["key_passphrase_map"], $this->app["env"]);
        $process = new Process($node);
        $this->assertEquals("<fg=yellow> on </fg=yellow><fg=yellow;options=bold>localhost</fg=yellow;options=bold>", $process->getNodeInfo());
    }

    public function testIsMaster()
    {
        $node = new Node("localhost", $this->app["key_passphrase_map"], $this->app["env"]);
        $process = new Process($node);
        $this->assertEquals(false, $process->isMaster());

        $process = Process::createMasterProcess();
        $this->assertEquals(true, $process->isMaster());
    }

    public function testPid()
    {
        $node = new Node("localhost", $this->app["key_passphrase_map"], $this->app["env"]);
        $process = new Process($node);

        if (!function_exists('posix_getpid')) {
            $pid = getmypid();
        } else {
            $pid = posix_getpid();
        }

        $this->assertEquals($pid, $process->pid());
    }

    public function testNode()
    {
        $node = new Node("localhost", $this->app["key_passphrase_map"], $this->app["env"]);
        $process = new Process($node);

        $this->assertEquals($node->getName(), $process->node()->getName());
    }
}