<?php
namespace Test\Altax\Module\Server\Resource;

use Altax\Module\Server\Resource\KeyPassphraseMap;

class KeyPassphraseMapTest extends \PHPUnit_Framework_TestCase
{
	public function testDefault()
    {
    	$keyPath = __DIR__."/../../../../keys/id_rsa_pass";
    	$keyPath2 = __DIR__."/../../../../keys/id_rsa_nopass";

    	KeyPassphraseMap::getSharedInstance()->setPassphraseAtKey($keyPath, "passpass");
    	
    	$this->assertEquals("passpass", KeyPassphraseMap::getSharedInstance()->getPassphraseAtKey($keyPath));

     	$this->assertEquals(null, KeyPassphraseMap::getSharedInstance()->getPassphraseAtKey($keyPath2));

     	$this->assertEquals(true, KeyPassphraseMap::getSharedInstance()->hasPassphraseAtKey($keyPath));

     	$this->assertEquals(false, KeyPassphraseMap::getSharedInstance()->hasPassphraseAtKey($keyPath2));

    }
}
