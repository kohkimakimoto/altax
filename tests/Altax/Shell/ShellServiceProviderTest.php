<?php
namespace Test\Altax\Shell;

use Altax\Shell\CommandBuilder;
use Altax\Shell\ScriptBuilder;

class ShellServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
    }

    public function testCommandBuilder()
    {
        $obj = $this->app["shell.command"];
        $this->assertTrue($obj instanceof CommandBuilder);
    }

    public function testScriptBuilder()
    {
        $obj = $this->app["shell.script"];
        $this->assertTrue($obj instanceof ScriptBuilder);
    }

}
