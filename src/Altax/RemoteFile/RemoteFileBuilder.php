<?php
namespace Altax\RemoteFile;

class RemoteFileBuilder
{
    protected $process;
    protected $node;
    protected $output;

    public function __construct($process, $output)
    {
        $this->process = $process;
        $this->node = $process->getNode();
        $this->output = $output;
    }

    public function get($remote, $local)
    {
    }

    public function getString($remote)
    {
    }

    public function put($local, $remote)
    {
    }

    public function putString($remote, $contents)
    {
    }
}