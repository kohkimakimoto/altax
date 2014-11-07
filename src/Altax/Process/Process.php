<?php
namespace Altax\Process;

class Process
{
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function __toString()
    {
        return $this->name;
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

    public function pid()
    {
        return $this->getPid();
    }

    public function isMaster()
    {
        return false;
    }

    public function getName()
    {
        return $this->name;
    }
}
