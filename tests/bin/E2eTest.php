<?php
namespace Test\bin;

use Symfony\Component\Process\Process;

/**
 * End to End test for command line application.
 * Note: Can't trac coverage of running code.
 */
class E2eTest extends \PHPUnit_Framework_TestCase
{
    public function testRunTaskTest001()
    {   
        $bin = realpath(__DIR__."/../../bin/altax");
        $currentDir = __DIR__;
        $process = new Process("cd $currentDir && php $bin test001");
        $process->run();
        $this->assertEquals(true, $process->isSuccessful());

        $this->assertEquals("This is a test001\n", $process->getOutput());
    }
}