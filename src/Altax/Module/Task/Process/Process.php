<?php
namespace Altax\Module\Task\Process;

use Symfony\Component\Process\Process as SymfonyProcess;

class Process
{
    protected $commandline;
    protected $task;

    public function setTask($task)
    {
        $this->task = $task;
    }

    public function __construct($commandline, $cwd = null, array $env = null, $stdin = null, $timeout = 60, array $options = array())
    {
        $this->commandline = $commandline;
    }

    public function run()
    {
        $self = $this;

        $symfonyProcess = new SymfonyProcess($this->commandline);
        $symfonyProcess->setTimeout(null);
        $symfonyProcess->run(function ($type, $buffer) use ($self) {
            $self->task->getOutput()->write($buffer);
        });
    }
}