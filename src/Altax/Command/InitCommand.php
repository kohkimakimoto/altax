<?php
namespace Altax\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('init')
            ->setDescription('Create default configuration directory and file.')
            ->addOption(
                'path',
                null,
                InputOption::VALUE_REQUIRED,
                'Creating configuration file path'
                )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

    }

}