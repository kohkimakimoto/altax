<?php
namespace Altax\Shell;

use Symfony\Component\Process\Process as SymfonyProcess;
use Symfony\Component\Filesystem\Filesystem;
use Altax\Facade\Command;
use Altax\Facade\RemoteFile;

class Script
{
    protected $path;
    protected $process;
    protected $node;
    protected $output;
    protected $options = array();
    protected $env;
    protected $source;
    protected $working;
    protected $dest;

    public function __construct($path, $process, $output, $env)
    {
        $this->path = $path;
        $this->process = $process;
        $this->node = $process->getNode();
        $this->output = $output;
        $this->env = $env;
        $this->source = null;

        $paths = $this->env->get("script.paths", []);
        foreach ($paths as $scriptDir) {
            $p = $scriptDir."/".$path;
            if (is_file($p)) {
                $this->source = $p;
            }
        }

        if ($this->source === null) {
            throw new \InvalidArgumentException("Unknow script path '$path'.");
        }

        $this->working = $this->env->get("script.working")."/".uniqid();
        $this->dest = $this->working."/".basename($this->source);

        if ($this->output->isDebug()) {
            $this->output->writeln("Found script: ".$this->source);
        }
    }

    public function run()
    {
        if ($this->process->isMain()) {
            return $this->runLocally();
        }

        // copy script
        $v = $this->output->getVerbosity();
        $this->output->setVerbosity(0);
        if (Command::run("test -d ".$this->working)->isFailed()) {

            $this->output->setVerbosity($v);
            if ($this->output->isDebug()) {
                $this->output->writeln(
                    "Created working directory: ".$this->working
                    .$this->process->getNodeInfo());
            }
            $this->output->setVerbosity(0);

            Command::run("mkdir -p ".$this->working);
        }

        RemoteFile::put($this->source, $this->dest);
        $this->output->setVerbosity($v);
        if ($this->output->isDebug()) {
            $this->output->writeln(
                "Put script: ".$this->dest." (from ".$this->source.")"
                .$this->process->getNodeInfo());
        }
        $this->output->setVerbosity(0);

        $realCommand = $this->compileExecutedCommand($this->dest);

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

        $this->output->setVerbosity($v);
        if ($this->output->isDebug()) {
            $this->output->writeln(
                "<info>Run script: </info>".$this->path." (actually: <comment>$realCommand</comment>)"
                .$this->process->getNodeInfo());
        } else {
            $this->output->writeln(
                "<info>Run script: </info>".$this->path
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

        // remove script
        $this->output->setVerbosity(0);
        Command::run("rm -rf ".$this->working);
        $this->output->setVerbosity($v);

        if ($this->output->isDebug()) {
            $this->output->writeln("Removed working directory: ".$this->working);
        }

        return $result;
    }

    public function runLocally()
    {
        // copy script
        $fs = new Filesystem();
        if (!is_dir($this->working)) {
            $fs->mkdir($this->working);
            if ($this->output->isDebug()) {
                $this->output->writeln("Created working directory: ".$this->working);
            }
        }

        $fs->copy($this->source, $this->dest, true);
        if ($this->output->isDebug()) {
            $this->output->writeln("Created script: ".$this->dest);
        }

        $realCommand = $this->compileExecutedCommand($this->dest);

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
                "<info>Run script: </info>".$this->path." (actually: <comment>$realCommand</comment>)");
        } else {
            $this->output->writeln(
                "<info>Run script: </info>".$this->path);
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

        // remove script
        $fs->remove($this->working);
        if ($this->output->isDebug()) {
            $this->output->writeln("Removed working directory: ".$this->working);
        }

        return $result;
    }

    protected function compileExecutedCommand($dest)
    {
        $realCommand = "";

        if (isset($this->options["user"])) {
            $realCommand .= 'sudo -u'.$this->options["user"].' TERM=dumb ';
        }

        $interpreter = "/bin/bash -l";
        if (isset($this->options["interpreter"])) {
            $interpreter = $this->options["interpreter"];
        }

        if (isset($this->options["cwd"])) {
            $realCommand .= 'cd '.$this->options["cwd"].' && ';
        }

        $realCommand .= $interpreter." ";
        $realCommand .= str_replace('"', '\"', $dest);

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

    public function interpreter($value)
    {
        return $this->setOption("interpreter", $value);
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
