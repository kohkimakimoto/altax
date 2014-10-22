<?php
namespace Test\Altax\RemoteFile;

use Symfony\Component\Console\Output\BufferedOutput;

class CommandTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
        $this->app->instance("output", new BufferedOutput());
    }

    public function testPutError()
    {
        $remoteFileBuilder = $this->app["remote_file"];
        $remoteFile = $remoteFileBuilder->make();
        try {
            $remoteFile->put(__DIR__."/files/puttext.txt", "/tmp/puttext.txt");
            $this->assertEquals(true, false);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testPut()
    {
        $servers = $this->app['servers'];
        $servers->node("127.0.0.1");
        $env = $this->app['env'];
        $env->set('process.parallel', false);

        $executor = $this->app['process.executor'];
        $executor->on(["127.0.0.1"], function(){

            $remoteFileBuilder = $this->app["remote_file"];
            $remoteFile = $remoteFileBuilder->make();
            $remoteFile->put(__DIR__."/files/puttext.txt", __DIR__."/../../tmp/puttext.txt");

        });

        $this->assertTrue(file_exists(__DIR__."/../../tmp/puttext.txt"));
        unlink(__DIR__."/../../tmp/puttext.txt");
    }

}