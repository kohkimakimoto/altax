<?php
namespace Test\Altax\Server;

use Altax\Server\KeyPassphraseMap;

class KeyPassphraseMapTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
    }

    public function testDefault()
    {
        $keyPath = __DIR__."/../../keys/id_rsa_pass";
        $keyPath2 = __DIR__."/../../keys/id_rsa_nopass";

        $keyPassphraseMap = $this->app["key_passphrase_map"];
        $keyPassphraseMap->setPassphraseAtKey($keyPath, "passpass");

        $this->assertEquals("passpass", $keyPassphraseMap->getPassphraseAtKey($keyPath));

        $this->assertEquals(null, $keyPassphraseMap->getPassphraseAtKey($keyPath2));

        $this->assertEquals(true, $keyPassphraseMap->hasPassphraseAtKey($keyPath));

        $this->assertEquals(false, $keyPassphraseMap->hasPassphraseAtKey($keyPath2));
    }
}
