<?php
namespace Test\Altax\Util;

use Altax\Util\SSHConfig;

class SSHConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testParse() {
        $config = SSHConfig::parse(file_get_contents(__DIR__."/SSHConfigTest/ssh_config"));
//        print_r($config);
        $this->assertEquals(2, count($config));
        $this->assertEquals("192.168.56.1", $config["test-server1"]["hostname"]);
        $this->assertEquals("192.168.56.2", $config["test-server2"]["hostname"]);
    }

    public function testParseFromFiles() {
        $config = SSHConfig::parseFromFiles(array(
            __DIR__."/SSHConfigTest/ssh_config",
            __DIR__."/SSHConfigTest/ssh_config2",
            __DIR__."/SSHConfigTest/ssh_config3"

        ));
//        print_r($config);
        $this->assertEquals(5, count($config));
        $this->assertEquals("192.168.56.1", $config["test-server1"]["hostname"]);
        $this->assertEquals("192.168.56.2", $config["test-server2"]["hostname"]);
    }

    public function testParseToNodeOptionsFormFiles()
    {
        $config = SSHConfig::parseToNodeOptionsFromFiles(array(
            __DIR__."/SSHConfigTest/ssh_config",
            __DIR__."/SSHConfigTest/ssh_config2",
            __DIR__."/SSHConfigTest/ssh_config3"
        ));
//        print_r($config);
        $this->assertEquals("192.168.56.2", $config["test-server5"]["host"]);
        $this->assertEquals("22", $config["test-server5"]["port"]);
        $this->assertEquals("kohkimakimoto", $config["test-server5"]["username"]);
        $this->assertEquals("~/.ssh/id_rsa", $config["test-server5"]["key"]);
        $this->assertEquals("web", $config["test-server5"]["roles"][0]);
        $this->assertEquals("db", $config["test-server5"]["roles"][1]);
    }

    public function testParseWithSpeceialComment() {
        $config = SSHConfig::parse(file_get_contents(__DIR__."/SSHConfigTest/ssh_config4"));
//        print_r($config);
        $this->assertEquals("true", $config["test-server1"]["altax.ignore"]);
    }

}