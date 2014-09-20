<?php
namespace Altax\Process;

class Process
{
    protected $servers;

    public function __construct($servers)
    {
        $this->servers = $servers;
    }
}
