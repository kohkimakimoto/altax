<?php
namespace Kohkimakimoto\Altax\Application;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Kohkimakimoto\Altax\Command\InitCommand; 
use Kohkimakimoto\Altax\Command\ConfigCommand;
use Kohkimakimoto\Altax\Command\TaskCommand;  
use Kohkimakimoto\Altax\Util\Context;

/**
 * Altax
 * @author Kohki Makimoto <kohki.makimoto@gmail.com>
 */
class AltaxApplication extends Application
{
    const VERSION = '2.0';
    const HELP_MESSAGES  =<<<EOL

<info>%s</info> version <comment>%s</comment>

Altax is a simple deployment tool running SSH in parallel.
Copyright (c) Kohki Makimoto <kohki.makimoto@gmail.com>
Apache License 2.0
EOL;

    protected $homeConfigurationPath = null;
    protected $defaultConfigurationPath = null;

    protected $loadedConfiguration = false;

    public function __construct($name = "Altax", $version = self::VERSION)
    {
        parent::__construct($name, $version);

        // Initilize Context of this application.
        Context::initialize();

        // Register builtin commands.
        $this->addCommands(array(
          new InitCommand(),
          new ConfigCommand()
        ));

        // Initialize aplication parameters.
        $this->homeConfigurationPath = getenv("HOME")."/.altax/altax.php";
        $this->defaultConfigurationPath = getcwd()."/.altax/altax.php";
    }
    
    /**
     * Override doRun method for Altax.
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->loadConfiguration($input, $output);
        $this->registerTasks();

        return parent::doRun($input, $output);
    }

    public function loadConfiguration(InputInterface $input, OutputInterface $output)
    {
        if ($this->loadedConfiguration === true) {
            return;
        }

        // Load configuration.
        // At first, load user home setting.
        $configurationPath = $this->getHomeConfigurationPath();
        if (is_file($configurationPath)) {
            include_once $configurationPath;
        }

        // At second, load current working directory setting.
        $configurationPath = $this->getDefaultConfigurationPath();
        if (is_file($configurationPath)) {
            include_once $configurationPath;
        }

        // At third, load specified file by a option.
        if ($input->hasOption("file")) {
            $configurationPath = $input->getOption("file");
            if ($configurationPath && is_file($configurationPath)) {
                include_once $configurationPath;
            } else if ($configurationPath) {
                throw new \RuntimeException("$configurationPath not found");
            }
        }
        
        $this->loadedConfiguration = true;
    }

    protected function registerTasks()
    {
        $context = $this->getContext();
        $tasks = $context->get("tasks");
        foreach ($tasks as $taskName => $task) {
            $taskCommand = new TaskCommand($taskName);
            $taskCommand->configureByTask($task);
            $this->add($taskCommand);
        }
    }


    public function getHomeConfigurationPath()
    {
        return $this->homeConfigurationPath;
    }

    public function setHomeConfigurationPath($homeConfigurationPath)
    {
        $this->homeConfigurationPath = $homeConfigurationPath;
    }

    public function getDefaultConfigurationPath()
    {
        return $this->defaultConfigurationPath;
    }

    public function setDefaultConfigurationPath($defaultConfigurationPath)
    {
        $this->defaultConfigurationPath = $defaultConfigurationPath;
    }

    protected function getDefaultInputDefinition()
    {
        $definition = parent::getDefaultInputDefinition();

        // Additional options used by Altax.
        $definition->addOptions(array(
            new InputOption('--file',  '-f', InputOption::VALUE_REQUIRED, 'Specify to load configuration file.'),
            new InputOption('--debug', '-d', InputOption::VALUE_NONE, 'Switch the debug mode to output log on the debug level.'),
        ));

        return $definition;
    }

    public function getContext()
    {
        return Context::getInstance();
    }

    public function getLongVersion()
    {
        return sprintf(self::HELP_MESSAGES, $this->getName(), $this->getVersion());
    }


}


