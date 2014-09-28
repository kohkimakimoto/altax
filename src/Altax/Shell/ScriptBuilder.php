<?php
namespace Altax\Shell;

class ScriptBuilder
{
    protected $runtime;
    protected $output;
    protected $env;

    public function __construct($runtime, $output, $env)
    {
        $this->runtime = $runtime;
        $this->output = $output;
        $this->env = $env;
    }

    public function make($path)
    {
        return new Script($path, $this->process, $this->output, $this->env);
    }

    public function run($path, $options = array())
    {
        $script = $this->make($path);
        if (isset($options["cwd"])) {

            $script->cwd($options["cwd"]);
        }
        if (isset($options["user"])) {
            $script->cwd($options["user"]);
        }
        if (isset($options["timeout"])) {
            $script->timeout($options["timeout"]);
        }
        if (isset($options["output"])) {
            $script->output($options["output"]);
        }
        if (isset($options["with"])) {
            $script->with($options["with"]);
        }

        return $script->run();
    }

    public function runLocally($path, $options = array())
    {
        $script = $this->make($path);
        if (isset($options["cwd"])) {

            $script->cwd($options["cwd"]);
        }
        if (isset($options["user"])) {
            $script->cwd($options["user"]);
        }
        if (isset($options["timeout"])) {
            $script->timeout($options["timeout"]);
        }
        if (isset($options["with"])) {
            $script->with($options["with"]);
        }

        return $script->runLocally();
    }
}
