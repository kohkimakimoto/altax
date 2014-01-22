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
    protected $container;

    public function __construct(\Altax\Foundation\Container $container)
    {
        parent::__construct($container->getName(), $container->getVersion());
        $this->container = $container;
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        // Init altax application
        $this->initContainer($input, $output);

        $this->registerBaseCommands();

        $this->registerAliases();

        $this->loadConfiguration($input, $output);

        // Runs specified command under the symfony console.
        return parent::doRun($input, $output);
    }

    /**
     * Initialize altax application container
     */
    protected function initContainer(InputInterface $input, OutputInterface $output)
    {
        // Addtional specified configuration file.
        if (true === $input->hasParameterOption(array('--file', '-f'))) {
            $this->container->setConfigFile("option", $input->getParameterOption(array('--file', '-f')));
        }
    }

    protected function loadConfiguration(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->container->getConfigFiles() as $key => $file) {
            if ($file && is_file($file)) {
                require_once $file;
            }
        }
    }
    protected function registerAliases()
    {
        \Altax\Foundation\AliasLoader::getInstance(Array(
            'Task' => 'Altax\Facades\Task',
            ))->register();
    }
    /**
     * Register base commands
     */
    protected function registerBaseCommands()
    {
        $finder = new Finder();
        $finder->files()->name('*Command.php')->in(__DIR__."/../Command");
        foreach ($finder as $file) {

            if ($file->getFilename() === "BaseCommand.php") {
                continue;
            }

            $class = "Altax\Command\\".$file->getBasename('.php');
            $r = new \ReflectionClass($class);
            $command = $r->newInstance();
            $command->setApplication($this);
            $this->add($command);

        }
    }

    public function getLongVersion()
    {
        return sprintf(self::HELP_MESSAGES, $this->container->getName(), $this->container->getVersion());
    }

    public function getContainer()
    {
        return $this->container;
    }
}