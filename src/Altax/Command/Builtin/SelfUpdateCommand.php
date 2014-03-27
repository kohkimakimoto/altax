<?php
namespace Altax\Command\Builtin;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Roles Command
 */
class SelfUpdateCommand extends \Symfony\Component\Console\Command\Command
{
    protected function configure()
    {
        $this
            ->setName('self-update')
            ->setAliases(array('selfupdate'))
            ->setDescription('Updates altax to the latest version.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }

}