<?php
namespace Altax\Filesystem;

use Altax\Process\NodeProcess;

class Filesystem
{
    protected $commandBuilder;
    protected $process;
    protected $node;
    protected $output;

    public function __construct($commandBuilder, $process, $output)
    {
        if (!($process instanceof NodeProcess)) {
            throw new \InvalidArgumentException("You must use 'Filesystem' in the NodeProcess");
        }

        $this->commandBuilder = $commandBuilder;
        $this->process = $process;
        $this->node = $process->getNode();
        $this->output = $output;
    }

    public function exists($path)
    {
        $command = $this->commandBuilder->make("test -e $path");
        $verbosity = $command->getOutput()->getVerbosity();

        // Prevents to display command output.
        $command->getOutput()->setVerbosity(0);
        $ret = $command->run()->isSuccessful();
        $command->getOutput()->setVerbosity($verbosity);

        if ($ret) {
            $this->output->writeln("<info>File: </info>$path (exists)".$this->process->getNodeInfo());
        } else {
            $this->output->writeln("<info>File: </info>$path (not exists)".$this->process->getNodeInfo());
        }

        return $ret;
    }

    public function remove($paths)
    {
        if (!is_array($paths)) {
            $paths = array($paths);
        }

        foreach ($paths as $path) {
            $command = $this->commandBuilder->make("rm -rf $path");
            $verbosity = $command->getOutput()->getVerbosity();

            // Prevents to display command output.
            $command->getOutput()->setVerbosity(0);
            $ret = $command->run()->isSuccessful();
            $command->getOutput()->setVerbosity($verbosity);

            if ($ret) {
                $this->output->writeln("<info>File: </info>$path (removed)".$this->process->getNodeInfo());
            } else {
                $this->output->writeln("<info>File: </info>$path (coundn't remove)".$this->process->getNodeInfo());
            }
        }
    }
}
