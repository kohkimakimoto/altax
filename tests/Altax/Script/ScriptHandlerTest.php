<?php
namespace Test\Altax\Script;

use Altax\Script\ScriptHandler;

class ScriptHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testRemoveAutoloadFiles()
    {
        ScriptHandler::removeAutoloadFiles();
    }
}