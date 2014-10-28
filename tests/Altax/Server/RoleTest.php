<?php
namespace Test\Altax\Server;

use Altax\Server\Role;

class RoleTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
    }

    public function testGetAndSet()
    {
        $role = new Role("web");
        $role->setName("web2");
        $this->assertEquals("web2", $role->getName());
        $this->assertEquals("web2", $role);
    }
}