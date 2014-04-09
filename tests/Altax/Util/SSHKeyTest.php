<?php
namespace Test\Altax\Util;

use Altax\Util\SSHKey;

class SSHKeyTest extends \PHPUnit_Framework_TestCase
{

    public function testHasPassphrase()
    {
        $ret = SSHKey::hasPassphrase(file_get_contents( __DIR__."/../../keys/id_rsa_nopass"));
        $this->assertEquals(false, $ret);

        $ret = SSHKey::hasPassphrase(file_get_contents( __DIR__."/../../keys/id_rsa_pass"));
        $this->assertEquals(true, $ret);
    }
}
