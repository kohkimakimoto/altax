<?php
namespace Altax\RemoteFile;

class RemoteFileBuilder
{
    protected $runtime;
    protected $output;

    public function __construct($runtime, $output)
    {
        $this->runtime = $runtime;
        $this->output = $output;
    }

    public function make()
    {
        return new RemoteFile(
            $this->runtime->getProcess(),
            $this->output);
    }

    public function get($remote, $local)
    {
        $remoteFile = $this->make();

        return $remoteFile->get($remote, $local);
    }

    public function getString($remote)
    {
        $remoteFile = $this->make();

        return $remoteFile->getString($remote);
    }

    public function put($local, $remote)
    {
        $remoteFile = $this->make();

        return $remoteFile->put($local, $remote);
    }

    public function putString($remote, $contents)
    {
        $remoteFile = $this->make();

        return $remoteFile->putString($remote, $contents);
    }

}
