<?php
namespace Altax\Shell;

class ScriptBuilder
{
    protected $process;
    protected $node;
    protected $output;
    protected $env;

    public function __construct($process, $output, $env)
    {
        $this->process = $process;
        $this->node = $process->getNode();
        $this->output = $output;
        $this->env = $env;
    }

    public function make($path)
    {
        return new Script($path, $this->process, $this->output, $this->env);
    }
}