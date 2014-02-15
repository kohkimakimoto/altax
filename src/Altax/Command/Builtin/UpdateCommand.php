<?php
namespace Altax\Command\Builtin;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Composer\Console\Application as ComposerApplication;
use Composer\IO\ConsoleIO;
use Composer\Factory;

class UpdateCommand extends \Composer\Command\UpdateCommand
{
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $io = new ConsoleIO($input, $output, $this->getHelperSet());
        $composer = Factory::create($io);
        $this->setComposer($composer);
        $this->setIO($io);
    }
}