<?php
namespace Altax\Process;

class MasterProcess extends Process
{
    public function __construct()
    {
        parent::__construct("master");
    }

    public function isMaster()
    {
        return true;
    }
}