<?php

namespace Altax\Console;

use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


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

    public function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->app->boot();
    }
}