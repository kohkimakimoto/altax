<?php
namespace Altax\Shell;

use Symfony\Component\Process\Process as SymfonyProcess;

class Command
{
    protected $commandline;
    protected $process;
    protected $node;
    protected $output;
    protected $options = array();

    public function __construct($commandline, $process, $output, $env)
    {
        $this->commandline = $commandline;
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

        if (!$this->node) {
            throw new \RuntimeException("Node is not defined to run the command.");
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

        $outputType = "quiet";
        if (isset($this->options["output"])) {
            $outputType = $this->options["output"];
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

        $ssh->exec($realCommand, function ($buffer) use ($output, $outputType, &$resultContent) {
            if ($outputType == "stdout" || $output->isDebug()) {
                $output->write($buffer);
            }
            $resultContent .= $buffer;
        });

        $returnCode = $ssh->getExitStatus();

        $result = new CommandResult($returnCode, $resultContent);
        if ($result->isFailed() && $outputType === 'quiet') {
            $output->writeln($result->getContents());
        }

        return $result;
    }

    public function runLocally()
    {
        $commandline = $this->commandline;

        if (is_array($commandline)) {
            $commandline = implode(" && ", $commandline);
        }

        $realCommand = $this->compileRealCommand($commandline);

        $symfonyProcess = new SymfonyProcess($realCommand);
        if (isset($this->options["timeout"])) {
            $symfonyProcess->setTimeout($this->options["timeout"]);
        } else {
            $symfonyProcess->setTimeout(null);
        }

        $outputType = "quiet";
        if (isset($this->options["output"])) {
            $outputType = $this->options["output"];
        }

        if ($this->output->isDebug()) {
            $this->output->writeln(
                "<info>Run command: </info>$commandline (actually: <comment>$realCommand</comment>)");
        } else {
            $this->output->writeln(
                "<info>Run command: </info>$commandline");
        }

        $output = $this->output;
        $resultContent = null;
        $returnCode = $symfonyProcess->run(function ($type, $buffer) use ($output, $outputType, &$resultContent) {
            if ($outputType == "stdout" || $output->isDebug()) {
                $output->write($buffer);
            }
            $resultContent .= $buffer;
        });

        $result = new CommandResult($returnCode, $resultContent);
        if ($result->isFailed() && $outputType === 'quiet') {
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

    public function output($value)
    {
        if ($value !== "stdout" && $value !== "quiet" && $value !== "progress") {
            throw new \InvalidArgumentException("unsupported output option '$value'");
        }
        return $this->setOption("output", $value);
    }
    public function setOption($key, $value)
    {
        $this->options[$key] = $value;

        return $this;
    }
}