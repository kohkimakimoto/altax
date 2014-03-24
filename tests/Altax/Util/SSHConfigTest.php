<?php
namespace Test\Altax\Util;

use Altax\Util\SSHConfig;

class SSHConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testParse() {
        $config = SSHConfig::parse(file_get_contents(__DIR__."/SSHConfigTest/ssh_config"));
//        print_r($config);
        $this->assertEquals(2, count($config));
        $this->assertEquals("192.168.56.1", $config["test-server1"]["HostName"]);
        $this->assertEquals("192.168.56.2", $config["test-server2"]["HostName"]);

    }
}