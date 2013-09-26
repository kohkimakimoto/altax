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
        // Load configuration.
        // At first, load user home setting.
        $configurationPath = getenv("HOME")."/.altax/altax.php";
        if (is_file($configurationPath)) {
            include_once $configurationPath;
        }

        // At second, load current working directory setting.
        $configurationPath = getcwd()."/.altax/altax.php";
        if (is_file($configurationPath)) {
            include_once $configurationPath;
        }

        // At third, load specified file by a option.
        $configurationPath = $input->getOption("file");
        if ($configurationPath && is_file($configurationPath)) {
            include_once $configurationPath;
        } else if ($configurationPath) {
            throw new \RuntimeException("$configurationPath not found");
        }

        /*
        $context = $this->getApplication()->getContext();
        print_r($context->getAttributesAsFlatArray());
        exit;
        */
    }
}
