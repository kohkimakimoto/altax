<?php
namespace Test\Altax\ComposerScript;

use Altax\ComposerScript\ScriptHandler;

class ScriptHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testRemoveAutoloadFiles()
    {
        ScriptHandler::removeAutoloadFiles();
    }
}
