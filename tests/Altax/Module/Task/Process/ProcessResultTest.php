<?php
namespace Test\Altax\Module\Task\Process;

use Altax\Module\Task\Process\ProcessResult;

class ProcessResultTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {

    }

    public function testIsFailed()
    {
        $processResult = new ProcessResult(1, "aaaa");
        $this->assertEquals(true, $processResult->isFailed());

        $processResult = new ProcessResult(0, "aaaa");
        $this->assertEquals(false, $processResult->isFailed());
    }

    public function testIsSuccessful()
    {
        $processResult = new ProcessResult(0, "aaaa");
        $this->assertEquals(true, $processResult->isSuccessful());

        $processResult = new ProcessResult(1, "aaaa");
        $this->assertEquals(false, $processResult->isSuccessful());
    }

    public function testGetContents()
    {
        $processResult = new ProcessResult(0, "aaaa");
        $this->assertEquals("aaaa", $processResult->getContents());
    }

    public function testToString()
    {
        $processResult = new ProcessResult(0, "aaaa");
        $this->assertEquals("aaaa", $processResult);
    }
}