<?php
namespace Kohkimakimoto\Altax\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

use Kohkimakimoto\Altax\Util\Context;
use Kohkimakimoto\Altax\Command\BaseCommand;

class ConfigCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('config')
            ->setDescription('Show configurations')
            ->setHelp('')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $context = $this->getApplication()->getContext();
        $output->writeln("<comment>Defined configurations</comment>");
        
        $attributes = $context->getAttributesAsFlatArray();
        foreach ($attributes as $key => $value) {
            if (is_callable($value)) {
                $v = "function()";
            } else if (is_array($value)) {
                $v = "array()";
            } else {
                $v = $value;
            }

            $output->writeln("<info>  $key</info> => $v");
        }
    }
}
