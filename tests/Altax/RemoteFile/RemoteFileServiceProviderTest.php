<?php
namespace Test\Altax\RemoteFile;

use Altax\RemoteFile\RemoteFileBuilder;

class RemoteFileProviderTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
    }

    public function testRemoteFile()
    {
        $obj = $this->app["remote_file"];
        $this->assertTrue($obj instanceof RemoteFileBuilder);
    }

}
