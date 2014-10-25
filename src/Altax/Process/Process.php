<?php
namespace Altax\Process;

class Process
{
    protected $node;

    protected $master = false;

    public static function createMasterProcess()
    {
        $process = new static(null);
        $process->master = true;

        return $process;
    }

    public function __construct($node)
    {
        $this->node = $node;
    }

    public function getNodeInfo()
    {
        return "<fg=yellow> on </fg=yellow><fg=yellow;options=bold>".$this->getNode()->getName()."</fg=yellow;options=bold>";
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

    public function isMaster()
    {
        return $this->master;
    }

    public function node()
    {
        return $this->getNode();
    }

    public function pid()
    {
        return $this->getPid();
    }
}
