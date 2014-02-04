<?php
namespace Altax\Module\Task\Process;

use Symfony\Component\Process\Process as SymfonyProcess;

class Process
{
    protected $runtimeTask;
    protected $commandline;

    public function __construct($runtimeTask)
    {
        $this->runtimeTask = $runtimeTask;
    }

    public function setCommandline($commandline)
    {
        $this->commandline = $commandline;
    }

    public function run()
    {
        $self = $this;

        $symfonyProcess = new SymfonyProcess($this->commandline);
        $symfonyProcess->setTimeout(null);
        $symfonyProcess->run(function ($type, $buffer) use ($self) {
            $self->runtimeTask->getOutput()->write($buffer);
        });
    }
}