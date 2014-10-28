<?php
namespace Altax\Filesystem;


class LocalFilesystem
{
    protected $commandBuilder;
    protected $process;
    protected $output;

    public function __construct($commandBuilder, $process, $output)
    {
        $this->commandBuilder = $commandBuilder;
        $this->process = $process;
        $this->output = $output;
    }
}