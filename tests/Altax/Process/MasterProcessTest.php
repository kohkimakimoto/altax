<?php
namespace Test\Altax\Process;

use Altax\Process\MasterProcess;
use Altax\Server\Node;

class MasterProcessTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
    }

    public function testPid()
    {
        $process = new MasterProcess();

        if (!function_exists('posix_getpid')) {
            $pid = getmypid();
        } else {
            $pid = posix_getpid();
        }

        $this->assertEquals($pid, $process->pid());
    }
}