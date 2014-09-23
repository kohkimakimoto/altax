<?php
namespace Test\Altax\Server;

use Altax\Server\ServerManager;
use Altax\Server\KeyPassphraseMap;

class ServerServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
    }

    public function testMakeServer()
    {
        $obj = $this->app["servers"];
        $this->assertTrue($obj instanceof ServerManager);
    }

    public function testMakeKeyPassphraseMap()
    {
        $obj = $this->app["key_passphrase_map"];
        $this->assertTrue($obj instanceof KeyPassphraseMap);
    }
}
