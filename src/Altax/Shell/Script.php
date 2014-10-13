<?php
namespace Altax\Shell;

use Symfony\Component\Process\Process as SymfonyProcess;
use Symfony\Component\Filesystem\Filesystem;

class Script
{
    protected $path;
    protected $commandBuilder;
    protected $remoteFileBuilder;
    protected $process;
    protected $node;
    protected $output;
    protected $options = array();
    protected $env;
    protected $working;
    protected $dest;

    public function __construct($path, $commandBuilder, $remoteFileBuilder, $process, $output, $env)
    {
        $this->path = $path;
        $this->commandBuilder = $commandBuilder;
        $this->remoteFileBuilder = $remoteFileBuilder;
        $this->process = $process;
        $this->node = $process->getNode();
        $this->output = $output;
        $this->env = $env;

        if (!is_file($this->path)) {
            throw new \InvalidArgumentException("Not found script '$path'.");
        }

        $this->working = $this->env->get("script.working")."/".uniqid();
        $this->dest = $this->working."/".basename($this->path);

        if ($this->output->isDebug()) {
            $this->output->writeln("Found script: ".$this->path);
        }
    }

    public function run()
    {
        if ($this->process->isMaster()) {
            return $this->runLocally();
        }

        // copy script
        $v = $this->output->getVerbosity();
        $this->output->setVerbosity(0);
        if ($this->commandBuilder->run("test -d ".$this->working)->isFailed()) {

            $this->output->setVerbosity($v);
            if ($this->output->isDebug()) {
                $this->output->writeln(
                    "Created working directory: ".$this->working
                    .$this->process->getNodeInfo());
            }
            $this->output->setVerbosity(0);

            $this->commandBuilder->run("mkdir -p ".$this->working);
        }
        $this->remoteFileBuilder->put($this->path, $this->dest);
        $this->output->setVerbosity($v);

        if ($this->output->isDebug()) {
            $this->output->writeln(
                "Put script: ".$this->dest." (from ".$this->path.")"
                .$this->process->getNodeInfo());
        }

        $commandline = $this->compileExecutedCommand($this->dest);
        $result = $this->commandBuilder->run($commandline);

        // remove script
        $this->output->setVerbosity(0);
        $this->commandBuilder->run("rm -rf ".$this->working);
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

        $fs->copy($this->path, $this->dest, true);
        if ($this->output->isDebug()) {
            $this->output->writeln("Put script: ".$this->dest);
        }

        $commandline = $this->compileExecutedCommand($this->dest);
        $result = $this->commandBuilder->run($commandline);

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

    public function interpreter($value)
    {
        return $this->setOption("interpreter", $value);
    }

    public function setOption($key, $value)
    {
        $this->options[$key] = $value;

        return $this;
    }
}
