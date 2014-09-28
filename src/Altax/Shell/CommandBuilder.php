<?php
namespace Altax\Shell;

class CommandBuilder
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

    public function make($commandline)
    {
        return new Command(
            $commandline,
            $this->runtime->getProcess(),
            $this->output,
            $this->env);
    }

    public function run($commandline, $options = array())
    {
        $command = $this->make($commandline);

        if (isset($options["cwd"])) {
            $command->cwd($options["cwd"]);
        }
        if (isset($options["user"])) {
            $command->cwd($options["user"]);
        }
        if (isset($options["timeout"])) {
            $command->timeout($options["timeout"]);
        }
        if (isset($options["output"])) {
            $command->output($options["output"]);
        }

        return $command->run();
    }

    public function runLocally($commandline, $options = array())
    {
        $command = $this->make($commandline);

        if (isset($options["cwd"])) {

            $command->cwd($options["cwd"]);
        }
        if (isset($options["user"])) {
            $command->cwd($options["user"]);
        }
        if (isset($options["timeout"])) {
            $command->timeout($options["timeout"]);
        }
        if (isset($options["output"])) {
            $command->output($options["output"]);
        }

        return $command->runLocally();
    }
}
