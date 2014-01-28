<?php
namespace Altax\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class NodesCommand extends \Altax\Command\BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('nodes')
            ->setDescription('Displays nodes')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $nodes = $container->get("nodes");
        print_r($nodes);
    }

}