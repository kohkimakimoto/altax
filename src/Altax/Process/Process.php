<?php
namespace Altax\Process;

use Symfony\Component\Filesystem\Filesystem;

class Process
{
    protected $node;

    protected $main = false;

    public static function createMainProcess()
    {
        $process = new static(null);
        $process->main = true;
        return $process;
    }

    public function __construct($node)
    {
        $this->node = $node;
    }

    public function getNodeInfo()
    {
        return "<info>[".$this->getNode()->getName()."]</info> ";
    }

    public function getNode()
    {
        return $this->node;
    }

    protected function getPid()
    {
        $pid = null;
        if (!function_exists('posix_getpid')) {
            $pid = getmypid();
        } else {
            $pid = posix_getpid();
        }

        return $pid;
    }


    public function get($remote, $local)
    {
        if (!$this->node) {
            throw new \RuntimeException("Node is not defined to get a file.");
        }

        $this->output->writeln($this->getRemoteInfoPrefix()."<info>Get: </info>$remote -> $local");

        $sftp = $this->getSFTP();

        if (!is_dir(dirname($local))) {
            $fs = new Filesystem();
            $fs->mkdir(dirname($local));
            if ($this->output->isDebug()) {
                $this->output->writeln($this->getRemoteInfoPrefix()."<info>Create directory: </info>".dirname($local));
            }
        }

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

        $this->output->writeln($this->getRemoteInfoPrefix()."<info>Get: </info>$remote");

        $sftp = $this->getSFTP();
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

        $this->output->writeln($this->getRemoteInfoPrefix()."<info>Put: </info>$local -> $remote");

        $sftp = $this->getSFTP();

        if (!is_file($local)) {
           throw new \RuntimeException("Couldn't put: $local -> $remote");
        }

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

        $this->output->writeln($this->getRemoteInfoPrefix()."<info>Put: </info>$remote");

        $sftp = $this->getSFTP();
        $ret = $sftp->put($remote, $contents);
        if ($ret === false) {
            throw new \RuntimeException("Couldn't put: $remote");
        }

        return $ret;
    }

    protected function getSFTP()
    {
        $sftp = new \Net_SFTP(
            $this->node->getHostOrDefault(),
            $this->node->getPortOrDefault());
        $key = new \Crypt_RSA();
        $key->loadKey($this->node->getKeyContents());
        if (!$sftp->login($this->node->getUsernameOrDefault(), $key)) {
            throw new \RuntimeException('Unable to login '.$this->node->getName());
        }

        return $sftp;
    }

    public function getNodeName()
    {
        $name = null;
        if (!$this->node) {
            $name = "localhost";
        } else {
            $name = $this->node->getName();
        }

        return $name;
    }


    public function getLocalInfoPrefix()
    {
        return "<info>[</info><comment>localhost:".$this->getPid()."</comment><info>]</info> ";
    }

    public function isMain()
    {
        return $this->main;
    }

}
