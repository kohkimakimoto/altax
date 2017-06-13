<?php

namespace Altax\Console;

use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Command\ListCommand;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Helper\DialogHelper;

use Altax\Foundation\ModuleFacade;
use Altax\Util\Str;

/**
 * Altax console application
 */
class Application extends SymfonyApplication
{

    const HELP_MESSAGES =<<<EOL
<info>%s</info> version <comment>%s</comment>

Altax is an extensible deployment tool for PHP.
Copyright (c) Kohki Makimoto <kohki.makimoto@gmail.com>
Apache License 2.0
EOL;

    /**
     * Application container instance.
     */
    protected $container;

    public function __construct(\Altax\Foundation\Container $container)
    {
        parent::__construct($container->getName(), $container->getVersionWithCommit());
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        // Add output formatter style used by embedded composer.
        if (null === $output) {
            $styles = \Composer\Factory::createAdditionalStyles();
            $formatter = new OutputFormatter(null, $styles);
            $output = new ConsoleOutput(ConsoleOutput::VERBOSITY_NORMAL, null, $formatter);
        }

        return parent::run($input, $output);
    }

    /**
     * This cli application main process.
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->configureContainer($input, $output);
        $this->registerBuiltinCommands();
        $this->registerBaseModules();
        $this->loadConfiguration($input, $output);
        $this->registerTasksAsConsoleCommands();

        // Runs specified command under the symfony console.
        return parent::doRun($input, $output);
    }

    public function all($namespace = null)
    {
        $commands = parent::all($namespace);

        // Remove hidden command to prevent listing commands by ListCommand
        foreach ($commands as $name => $command) {
            if (method_exists($command, "getDefinedTask")) {
                // Consider the command Altax\Command\Command instance
                $definedTask = $command->getDefinedTask();
                if ($definedTask->isHidden()) {
                    unset($commands[$name]);
                }
            }
        }

        return $commands;
    }

    /**
     * Configure container to use cli application.
     */
    protected function configureContainer(InputInterface $input, OutputInterface $output)
    {
        // Additional specified configuration file.
        if (true === $input->hasParameterOption(array('--file', '-f'))) {
            $this->container->setConfigFile("option", $input->getParameterOption(array('--file', '-f')));
        }

        $this->container->setApp($this);
        $this->container->setInput($input);
        $this->container->setOutput($output);
    }

    /**
     * Register base commands
     */
    protected function registerBuiltinCommands()
    {
        $finder = new Finder();
        $finder->files()->name('*Command.php')->in(__DIR__."/../Command/Builtin");
        foreach ($finder as $file) {
            if ($file->getFilename() === 'Command.php') {
                continue;
            }

            $class = "Altax\Command\Builtin\\".$file->getBasename('.php');
            $r = new \ReflectionClass($class);
            $command = $r->newInstance();
            $this->add($command);
        }
    }

    /**
     * Register Modules.
     */
    protected function registerBaseModules()
    {
        ModuleFacade::clearResolvedInstances();
        ModuleFacade::setContainer($this->container);

        $finder = new Finder();
        $finder->directories()->depth('== 0')->in(__DIR__."/../Module");
        foreach ($finder as $dir) {
            $module =  $dir->getBasename();

            $facadeClass = "\\Altax\\Module\\".$module."\\Facade\\".$module;
            $implClass = "\\Altax\\Module\\".$module."\\".$module."Module";

            $moduleName = $facadeClass::getModuleName();

            $r = new \ReflectionClass($implClass);
            $instance = $r->newInstance($this->container);

            // register module into container
            $this->container->addModule($moduleName, $instance);

            if (!class_exists($moduleName)) {
                class_alias($facadeClass, $moduleName);
            }
        }
    }

    /**
     * Load configuration.
     */
    protected function loadConfiguration(InputInterface $input, OutputInterface $output)
    {
        $command = $this->getCommandName($input);
        if ($command == 'require' || $command == 'install' || $command == 'update') {
            // These are composer task. so don't need to load configuration for altax.
            return;
        }

        foreach ($this->container->getConfigFiles() as $key => $file) {
            if ($file && is_file($file)) {
                require $file;
            }
        }
    }

    /**
     * [registerTasksAsConsoleCommands description]
     * @return [type] [description]
     */
    protected function registerTasksAsConsoleCommands()
    {
        $tasks = $this->container->get("tasks");

        if ($tasks && is_array($tasks)) {
            foreach ($tasks as $task) {
                $this->add($task->createCommandInstance());

            }
        }
    }

    /**
     * [getLongVersion description]
     * @return [type] [description]
     */
    public function getLongVersion()
    {
        return sprintf(self::HELP_MESSAGES, $this->container->getName(), $this->container->getVersionWithCommit());
    }

    public function getContainer()
    {
        return $this->container;
    }

    protected function getDefaultInputDefinition()
    {
        $definition = parent::getDefaultInputDefinition();
        $definition->addOptions(array(
            new InputOption('--file', '-f', InputOption::VALUE_REQUIRED, 'Specifies configuration file to load.')
        ));

        return $definition;
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultHelperSet()
    {
        $helperSet = parent::getDefaultHelperSet();

        $helperSet->set(new DialogHelper());
        return $helperSet;
    }
}
