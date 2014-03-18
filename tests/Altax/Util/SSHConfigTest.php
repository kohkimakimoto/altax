<?php
namespace Test\Altax\Util;

use Altax\Util\SSHConfig;

class SSHConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testParse() {
        $config = SSHConfig::parse(file_get_contents(__DIR__."/SSHConfigTest/ssh_config"));
//        print_r($config);
    }
}