<?php
namespace Test\Altax\Executor;

use Altax\Process\Executor;

class ProcessServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();

        $taskManager = $this->app["task"];
        $task = $taskManager->register("test", function(){});
        $this->app->instance('command', $task->makeCommand());
    }

    public function testExecutor()
    {
        $obj = $this->app["process.executor"];
        $this->assertTrue($obj instanceof Executor);
    }
}
