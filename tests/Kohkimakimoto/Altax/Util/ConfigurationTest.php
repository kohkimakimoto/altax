<?php
namespace Test\Kohkimakimoto\Altax\Util;

use Kohkimakimoto\Altax\Util\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $configuration = new Configuration();
    }

    public function testLoadHosts()
    {
        $configuration = new Configuration();
        $configuration->loadHosts(__DIR__."/ConfigurationTest/hosts.php");
        
        $configuration = new Configuration();
        $configuration->loadHosts(array(__DIR__."/ConfigurationTest/hosts.php"));
    }

    public function testLoadTasks()
    {
 //       $configuration = new Configuration();
//        $configuration->loadTasks(array(__DIR__."/ConfigurationTest/tasks"));
    }
}