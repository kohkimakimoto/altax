<?php
namespace Altax\Module\Task;

use Symfony\Component\Process\Process as SymfonyProcess;

class Process
{
    protected $commandline;

    public function __construct($commandline, $cwd = null, array $env = null, $stdin = null, $timeout = 60, array $options = array())
    {
        $this->commandline = $commandline;
    }

    public function run($commandline = null)
    {
        $symfonyProcess = new SymfonyProcess($commandline);
        $symfonyProcess->run();
    }
}