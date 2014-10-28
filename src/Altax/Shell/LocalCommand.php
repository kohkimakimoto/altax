<?php
namespace Altax\Shell;

use Symfony\Component\Process\Process as SymfonyProcess;

class LocalCommand
{
    protected $commandline;
    protected $process;
    protected $output;
    protected $options = array();
    protected $env;

    public function __construct($commandline, $process, $output, $env)
    {
        $this->commandline = $commandline;
        $this->process = $process;
        $this->output = $output;
        $this->env = $env;
    }

    public function run()
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

        if ($this->output->isDebug()) {
            $this->output->writeln(
                "<info>Run local command: </info>$commandline (actually: <comment>$realCommand</comment>)");
        } else {
            $this->output->writeln(
                "<info>Run local command: </info>$commandline");
        }

        $output = $this->output;
        $resultContent = null;
        $returnCode = $symfonyProcess->run(function ($type, $buffer) use ($output, &$resultContent) {
            if ($output->isVerbose()) {
                $output->write($buffer);
            }
            $resultContent .= $buffer;
        });

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
}
