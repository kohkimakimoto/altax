<?php
namespace Test\Altax\Task;

use Altax\Task\TaskServiceProvider;
use Altax\Task\TaskManager;

class TaskServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
    }

    public function testMakeTask()
    {
        $taskManager = $this->app["task"];
        $this->assertTrue($taskManager instanceof TaskManager);
    }
}

