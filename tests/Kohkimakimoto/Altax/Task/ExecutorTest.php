<?php
namespace Test\Kohkimakimoto\Altax\Task;

use Kohkimakimoto\Altax\Task\Executor;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Input\InputDefinition;

class ExecutorTest extends \PHPUnit_Framework_TestCase
{
    public function testDefault()
    {
        $executor = new Executor();
        $InputDefinition = new InputDefinition();
        $InputDefinition->addArgument(new InputArgument("args"));

        $input = new ArgvInput(array(), $InputDefinition);
        $input->setArgument("args", array());
        $output = new ConsoleOutput();

        host("127.0.0.1", "default");
        task('test_task',array('roles' => 'default'), function($host, $args){

          message("test ok");

        });

        $executor->execute("test_task", $input, $output);
    }
}
