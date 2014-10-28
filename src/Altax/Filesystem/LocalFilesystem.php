<?php
namespace Altax\Filesystem;

class LocalFilesystem
{
    protected $commandBuilder;
    protected $process;
    protected $output;

    public function __construct($commandBuilder, $process, $output)
    {
        $this->commandBuilder = $commandBuilder;
        $this->process = $process;
        $this->output = $output;
    }

    public function exists($path)
    {
        $ret = file_exists($path);

       if ($ret) {
            $this->output->writeln("<info>Check file: </info>$path (exists)");
        } else {
            $this->output->writeln("<info>Check file: </info>$path (not exists)");
        }

        return $ret;
    }
}
