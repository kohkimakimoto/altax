<?php
namespace Altax\Shell;

class Script
{
    protected $path;
    protected $process;
    protected $node;
    protected $output;
    protected $options = array();
    protected $env;

    public function __construct($path, $process, $output, $env)
    {
        $this->path = $path;
        $this->process = $process;
        $this->node = $process->getNode();
        $this->output = $output;
        $this->env = $env;
    }

    public function run()
    {
        if ($this->process->isMain()) {
            return $this->runLocally();
        }
    }

    public function runLocally()
    {

    }

    public function cwd($value)
    {
        return $this->setOption("cwd", $value);
    }

    public function user($value)
    {
        return $this->setOption("user", $value);
    }

    public function timeout($value)
    {
        return $this->setOption("timeout", $value);
    }

    public function output($value)
    {
        if ($value !== "stdout" && $value !== "quiet" && $value !== "progress") {
            throw new \InvalidArgumentException("unsupported output option '$value'");
        }

        return $this->setOption("output", $value);
    }

    public function with($value)
    {
        return $this->setOption("with", $value);
    }

    public function setOption($key, $value)
    {
        $this->options[$key] = $value;

        return $this;
    }
}
