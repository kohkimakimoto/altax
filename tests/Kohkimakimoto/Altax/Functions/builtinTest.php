<?php
namespace Test\Kohkimakimoto\Altax\Functions;

use Kohkimakimoto\Altax\Application\AltaxApplication;
use Kohkimakimoto\Altax\Util\Context;

class BuiltinFunctionsTest extends \PHPUnit_Framework_TestCase
{
    public function testSet()
    {
        $context = Context::initialize();

        set("test_val", "aaaaa");
        $this->assertEquals("aaaaa", $context->getParameter("test_val"));

    }

    public function testGet()
    {
        $context = Context::initialize();

        set("test_val", "ddddd");
        $this->assertEquals("ddddd", get("test_val"));

        set("test_val2", array("aaa" => array("bbb" => "ccc")));
        $this->assertEquals("ccc", get("test_val2/aaa/bbb"));
    }

    public function testBeforeException()
    {
        $context = Context::initialize();
        try {
            before('anything', 'task');
            $this->fail('before() should throw an exception if the first task does not exist');
        } catch (\Exception $e) {
            // Test passed
        }
        $context->set('tasks', array('anything' => array('callback')));
        try {
            before('anything', 'random');
            $this->fail('before() should throw an exception if the second task does not exist');
        } catch (\Exception $e) {
            // Test passed
        }
    }

    public function testBefore()
    {
        $task1 = 'task' . rand(0, 9);
        $task2 = 'task' . rand(10, 99);
        $task3 = 'task' . rand(100, 999);
        $tasks = array(
            $task1 => array('foo'),
            $task2 => array('bar'),
            $task3 => array('baz'),
        );
        $context = Context::initialize();
        $context->set('tasks', $tasks);
        before($task1, $task2);
        before($task1, $task3);
        before($task2, $task1);
        before($task2, $task3);
        $before = $context->get('before');
        $this->assertArrayHasKey($task1, $before);
        $this->assertTrue(in_array($task2, $before[$task1]));
        $this->assertTrue(in_array($task3, $before[$task1]));
        $this->assertArrayHasKey($task2, $before);
        $this->assertTrue(in_array($task1, $before[$task2]));
        $this->assertTrue(in_array($task3, $before[$task2]));
    }

    public function testAfterException()
    {
        $context = Context::initialize();
        try {
            after('anything', 'task');
            $this->fail('after() should throw an exception if the first task does not exist');
        } catch (\Exception $e) {
            // Test passed
        }
        $context->set('tasks', array('anything' => array('callback')));
        try {
            after('anything', 'random');
            $this->fail('after() should throw an exception if the second task does not exist');
        } catch (\Exception $e) {
            // Test passed
        }
    }

    public function testAfter()
    {
        $task1 = 'task' . rand(0, 9);
        $task2 = 'task' . rand(10, 99);
        $task3 = 'task' . rand(100, 999);
        $tasks = array(
            $task1 => array('foo'),
            $task2 => array('bar'),
            $task3 => array('baz'),
        );
        $context = Context::initialize();
        $context->set('tasks', $tasks);
        after($task1, $task2);
        after($task1, $task3);
        after($task2, $task1);
        after($task2, $task3);
        $after = $context->get('after');
        $this->assertArrayHasKey($task1, $after);
        $this->assertTrue(in_array($task2, $after[$task1]));
        $this->assertTrue(in_array($task3, $after[$task1]));
        $this->assertArrayHasKey($task2, $after);
        $this->assertTrue(in_array($task1, $after[$task2]));
        $this->assertTrue(in_array($task3, $after[$task2]));
    }

}
