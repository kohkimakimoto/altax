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

class BaseCommand extends Command
{
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        // Reload confiugraion if it need to do.
        $application = $this->getApplication();
        $application->loadConfiguration($input, $output);
    }
}