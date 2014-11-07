<?php
namespace Altax\Shell;

use Altax\Process\NodeProcess;

class Command
{
    protected $commandline;
    protected $process;
    protected $node;
    protected $output;
    protected $options = array();
    protected $env;

    public function __construct($commandline, $process, $output, $env)
    {
        if (!($process instanceof NodeProcess)) {
            throw new \InvalidArgumentException("You must use 'Command' in the NodeProcess");
        }

        $this->commandline = $commandline;
        $this->process = $process;
        $this->node = $process->getNode();
        $this->output = $output;
        $this->env = $env;
    }

    public function run()
    {
        if ($this->process->isMaster()) {
            throw new \RuntimeException("Command couldn't be used in master process.");
        }

        if (!$this->node) {
            throw new \RuntimeException("Node is not defined.");
        }

        $commandline = $this->commandline;

        if (is_array($commandline)) {
            $commandline = implode(" && ", $commandline);
        }

        $realCommand = $this->compileRealCommand($commandline);

        $ssh = $this->node->getSSHConnection();
        if (isset($this->options["timeout"])) {
            $ssh->setTimeout($this->options["timeout"]);
        } else {
            $ssh->setTimeout(null);
        }

        if ($this->output->isDebug()) {
            $this->output->writeln(
                "<info>Run command: </info>$commandline (actually: <comment>$realCommand</comment>)"
                .$this->process->getNodeInfo());
        } else {
            $this->output->writeln(
                "<info>Run command: </info>$commandline"
                .$this->process->getNodeInfo());
        }

        $output = $this->output;
        $resultContent = null;

        $ssh->exec($realCommand, function ($buffer) use ($output, &$resultContent) {
            if ($output->isVerbose()) {
                $output->write($buffer);
            }
            $resultContent .= $buffer;
        });

        $returnCode = $ssh->getExitStatus();

        $result = new CommandResult($returnCode, $resultContent);
        if ($result->isFailed()) {
            $output->writeln($result->getContents());
        }

        return $result;
    }

    protected function compileRealCommand($commandline)
    {
        $realCommand = "";

        if (isset($this->options["user"])) {
            $realCommand .= 'sudo -u'.$this->options["user"].' TERM=dumb ';
        }

        $sh = $this->env->get("command.shell", "/bin/bash -l -c");
        $realCommand .= $sh.' "';

        if (isset($this->options["cwd"])) {
            $realCommand .= 'cd '.$this->options["cwd"].' && ';
        }

        $realCommand .= str_replace('"', '\"', $commandline);
        $realCommand .= '"';

        return $realCommand;
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

    public function setOption($key, $value)
    {
        $this->options[$key] = $value;

        return $this;
    }

    public function getOutput()
    {
        return $this->output;
    }
}
