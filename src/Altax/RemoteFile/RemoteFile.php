<?php
namespace Altax\RemoteFile;

use Symfony\Component\Filesystem\Filesystem;

class RemoteFile
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
        if (!$this->node) {
            throw new \RuntimeException("Node is not defined to get a file.");
        }

        $sftp = $this->node->getSFTPConnection();

        if (!is_dir(dirname($local))) {
            $fs = new Filesystem();
            $fs->mkdir(dirname($local));
            $this->output->writeln(
                "<info>Created directory: </info>".dirname($local)
                );
        }

        $this->output->writeln(
            "<info>Get file: </info>$local (from <comment>$remote</comment>"
            .$this->process->getNodeInfo()
            .")");

        $ret = $sftp->get($remote, $local);
        if ($ret === false) {
            throw new \RuntimeException("Couldn't get: $remote -> $local");
        }

        return $ret;
    }

    public function getString($remote)
    {
        if (!$this->node) {
            throw new \RuntimeException("Node is not defined to get a file.");
        }

        $sftp = $this->node->getSFTPConnection();

        $this->output->writeln(
            "<info>Get file: </info>from $remote"
            .$this->process->getNodeInfo());

        $ret = $sftp->get($remote);
        if ($ret === false) {
            throw new \RuntimeException("Couldn't get: $remote");
        }

        return $ret;
    }

    public function put($local, $remote)
    {
        if (!$this->node) {
            throw new \RuntimeException("Node is not defined to put a file.");
        }

        $sftp = $this->node->getSFTPConnection();

        if (!is_file($local)) {
           throw new \RuntimeException("Couldn't put: $local -> $remote");
        }

        $this->output->writeln(
            "<info>Put file: </info>$remote"
            .$this->process->getNodeInfo()
            ." (from <comment>$local</comment>)"
            );

        $ret = $sftp->put($remote, $local, NET_SFTP_LOCAL_FILE);
        if ($ret === false) {
            throw new \RuntimeException("Couldn't put: $local -> $remote");
        }

        return $ret;
    }

    public function putString($remote, $contents)
    {
        if (!$this->node) {
            throw new \RuntimeException("Node is not defined to put a file.");
        }

        $sftp = $this->node->getSFTPConnection();

        $this->output->writeln(
            "<info>Put file: </info>$remote"
            .$this->process->getNodeInfo()
            );
        $ret = $sftp->put($remote, $contents);
        if ($ret === false) {
            throw new \RuntimeException("Couldn't put: $remote");
        }

        return $ret;
    }
}
