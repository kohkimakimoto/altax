<?php
namespace Altax\Process;

class Runtime
{
    protected $masterProcess;

    protected $process;

    public function setProcess($process)
    {
        $this->process = $process;
    }

    public function getProcess()
    {
        return $this->process;
    }

    public function bootMasterProcess()
    {
        $this->masterProcess = new MasterProcess;
        $this->process = $this->masterProcess;
    }

    public function backToMasterProcess()
    {
        $this->process = $this->masterProcess;
    }
}
