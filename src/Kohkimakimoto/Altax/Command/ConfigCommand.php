<?php
namespace Kohkimakimoto\Altax\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

use Kohkimakimoto\Altax\Util\Context;

class ConfigCommand extends Command
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
        $path = $this->getApplication()->getConfigPath();
        if (!is_file($path)) {
            throw new \RuntimeException("Not found $path");
        }

        $context = Context::initialize($path);

        $output->writeln("<comment>Configurations</comment>");

        $parameters = $context->getParametersFlatArray();
        foreach ($parameters as $key => $value) {
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
