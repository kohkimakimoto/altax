<?php
namespace Test\Altax\ComposerScript;

class FunctionTest extends \PHPUnit_Framework_TestCase
{
    public function testBootAltaxApplication()
    {
        $app = bootAltaxApplication();
    }

    public function testBootAltaxApplicationNotCli()
    {
        $app = bootAltaxApplication(array(), false);
    }

}