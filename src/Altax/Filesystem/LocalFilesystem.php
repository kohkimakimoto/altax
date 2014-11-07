<?php
namespace Altax\Filesystem;

use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;
use Symfony\Component\Filesystem\Exception\IOException;

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
            $this->output->writeln("<info>File: </info>$path (exists)");
        } else {
            $this->output->writeln("<info>File: </info>$path (not exists)");
        }

        return $ret;
    }

    public function remove($paths)
    {
        if (!is_array($paths)) {
            $paths = array($paths);
        }

        foreach ($paths as $path) {
            $ret = true;
            try {
                $sfs = new SymfonyFilesystem();
                $sfs->remove($paths);
            } catch (IOException $e) {
                $ret = false;
            }

            if ($ret) {
                $this->output->writeln("<info>File: </info>$path (removed)");
            } else {
                $this->output->writeln("<info>File: </info>$path (coundn't remove)");
            }
        }
    }

}
