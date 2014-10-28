<?php
namespace Altax\Filesystem;

class Filesystem
{
    protected $commandBuilder;
    protected $process;
    protected $node;
    protected $output;

    public function __construct($commandBuilder, $process, $output)
    {
        if (!($process instanceof NodeProcess)) {
            throw new \InvalidArgumentException("You must use 'Filesystem' in the NodeProcess");
        }

        $this->commandBuilder = $commandBuilder;
        $this->process = $process;
        $this->node = $process->getNode();
        $this->output = $output;
    }
}