<?php
namespace Test\Altax\Server;

use Altax\Server\Server;
use Altax\Server\KeyPassphraseMap;

class ServerServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
    }

    public function testMakeServer()
    {
        $server = $this->app["server"];
        $this->assertTrue($server instanceof Server);
    }

    public function testMakeKeyPassphraseMap()
    {
        $server = $this->app["key_passphrase_map"];
        $this->assertTrue($server instanceof KeyPassphraseMap);
    }
}
