<?php

namespace Altax\Console;

use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * Altax console application
 */
class Application extends SymfonyApplication
{
    const HELP_MESSAGES =<<<EOL
<info>%s</info> version <comment>%s</comment>

A simple deployment tool for PHP.
Copyright (c) Kohki Makimoto <kohki.makimoto@gmail.com>
Apache License 2.0
EOL;

    /**
     * Application instance.
     */
    protected $app;

    public function __construct(\Altax\Application\Application $app)
    {
        parent::__construct($app->getName(), $app->getVersion());
        $this->app = $app;
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        // Init altax application
        $this->initApplication($input, $output);

        $this->registerBaseCommands();

        $this->loadConfiguration($input, $output);

        // Runs specified command under the symfony console.
        return parent::doRun($input, $output);
    }

    /**
     * Initialize altax application container
     */
    protected function initApplication(InputInterface $input, OutputInterface $output)
    {
        // Addtional specified configuration file.
        if (true === $input->hasParameterOption(array('--file', '-f'))) {
            $this->app->setConfigFile("option", $input->getParameterOption(array('--file', '-f')));
        }
    }

    protected function loadConfiguration(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->app->getConfigFiles() as $key => $file) {
            if ($file && is_file($file)) {
                include $$file;
            }
        }
    }

    public function registerBaseCommands()
    {
        $finder = new Finder();
        $finder->files()->name('*Command.php')->in(__DIR__."/../Command");
        foreach ($finder as $file) {
            $class = "Altax\Command\\".$file->getBasename('.php');
            $r = new \ReflectionClass($class);
            $this->add($r->newInstance());
        }
    }

    public function getLongVersion()
    {
        return sprintf(self::HELP_MESSAGES, $this->app->getName(), $this->app->getVersion());
    }
}