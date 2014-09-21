<?php
namespace Altax\Process;

class ProcessExecutor
{
    protected $servers;

    protected $output;

    public function __construct($servers, $output)
    {
        $this->servers = $servers;
        $this->output = $output;
    }

    public function exec()
    {
        $args = func_get_args();
        if (count($args) === 0) {
            throw new \InvalidArgumentException("Missing argument. Must 1 arguments at minimum.");
        }

        if (count($args) === 1) {
            if ($args[0]) {
                if ($args[0] instanceof \Closure) {
                    $this->execClosure($args[0]);
                }
            }
        }
    }

    protected function execClosure($closure)
    {
        if ($this->output->isDebug()) {
            $this->output->writeln("Run closure");
        }
    }

}
