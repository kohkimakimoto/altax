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

    public function __construct(\Altax\Application\Application $app)
    {
        parent::__construct($app::NAME, $app::VERSION);
        $this->app = $app;
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        // Init altax application
        $this->initApplication($input, $output);

        // Runs specified command under the symfony console.
        return parent::doRun($input, $output);
    }

    /**
     * Initialize altax application container
     */
    public function initApplication(InputInterface $input, OutputInterface $output)
    {
        // Addtional specified configuration file.
        if (true === $input->hasParameterOption(array('--file', '-f'))) {
            $this->app->setConfigFile("option", $input->getParameterOption(array('--file', '-f')));

        }
    }
}