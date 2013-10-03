<?php
namespace Test\Kohkimakimoto\Altax\Task;

use Kohkimakimoto\Altax\Task\BaseTask;

class BaseTaskTest extends \PHPUnit_Framework_TestCase
{
    public function testDefault()
    {
        $myTestTask = new MyTestTask();
        $myTestTask->register();
    }
}

class MyTestTask extends BaseTask
{
    public function configure()
    {
        return array(
            "name"        => "mytest",
            "description" => "mytest",
            "roles" => "web"
            );
    }

    public function execute($host, $args)
    {
        $this->run("echo Hellow on the $host");
    }
}