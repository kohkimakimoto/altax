<?php

namespace Altax\Console;

use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Altax console application
 */
class Application extends SymfonyApplication
{
    protected $app;

    public function __construct($app = null)
    {
        if (!$app) {
            $app = new \Altax\Application\Application();
        }

        parent::__construct($app::NAME, $app::VERSION);

        $this->app = $app;
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        // Processes altax application initialize
        $this->initialize($input, $output);

        // Runs specified command under the symfony console.
        return parent::doRun($input, $output);
    }

    /**
     * Initialize altax application container
     */
    public function initialize(InputInterface $input, OutputInterface $output)
    {
        // Determine Loaded configuration files.

        // At first, load user home setting.
        $homeConfigurationPath = getenv("HOME")."/.altax/config.php";
        // At second, load current working directory setting.
        $defaultConfigurationPath = getcwd()."/.altax/config.php";
        // At third, load specified file by a option.
        $configurationPath = null;
        if (true === $input->hasParameterOption(array('--file', '-f'))) {
            $configurationPath = $input->getParameterOption(array('--file', '-f'));
        }

        //$this->app->register
    }
}