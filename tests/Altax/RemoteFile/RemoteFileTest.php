<?php
namespace Test\Altax\RemoteFile;

use Symfony\Component\Console\Output\BufferedOutput;

class RemoteFileTest extends \PHPUnit_Framework_TestCase
{
    public function setup()
    {
        $this->app = bootAltaxApplication();
        $this->app->instance("output", new BufferedOutput());
    }

    public function testPutErrorOnTheMasterProcess()
    {
        $remoteFileBuilder = $this->app["remote_file"];
        try {
            $remoteFile = $remoteFileBuilder->make();
            $this->assertEquals(true, false);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function testGet()
    {
        $servers = $this->app['servers'];
        $servers->node("127.0.0.1");
        $env = $this->app['env'];
        $env->set('process.parallel', false);

        $executor = $this->app['process.executor'];
        $executor->on(["127.0.0.1"], function(){

            $remoteFileBuilder = $this->app["remote_file"];
            $remoteFileBuilder->get(__DIR__."/files/text.txt", __DIR__."/../../tmp/text_get.txt");

        });

        $this->assertTrue(file_exists(__DIR__."/../../tmp/text_get.txt"));
        unlink(__DIR__."/../../tmp/text_get.txt");
    }

    public function testGetString()
    {
        $servers = $this->app['servers'];
        $servers->node("127.0.0.1");
        $env = $this->app['env'];
        $env->set('process.parallel', false);

        $executor = $this->app['process.executor'];
        $executor->on(["127.0.0.1"], function() {

            $remoteFileBuilder = $this->app["remote_file"];
            $buffer = $remoteFileBuilder->getString(__DIR__."/files/text.txt");
            $this->assertEquals("remotefile ok", $buffer);
        });
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
            $remoteFileBuilder->put(__DIR__."/files/text.txt", __DIR__."/../../tmp/text_put.txt");

        });

        $this->assertTrue(file_exists(__DIR__."/../../tmp/text_put.txt"));
        unlink(__DIR__."/../../tmp/text_put.txt");
    }

    public function testPutString()
    {
        $servers = $this->app['servers'];
        $servers->node("127.0.0.1");
        $env = $this->app['env'];
        $env->set('process.parallel', false);

        $executor = $this->app['process.executor'];
        $executor->on(["127.0.0.1"], function() {

            $remoteFileBuilder = $this->app["remote_file"];
            $remoteFileBuilder->putString(__DIR__."/../../tmp/text_put.txt", "remotefile ok");
        });

        $this->assertTrue(file_exists(__DIR__."/../../tmp/text_put.txt"));
        unlink(__DIR__."/../../tmp/text_put.txt");
    }

}