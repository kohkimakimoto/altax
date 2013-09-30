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
        $context = Context::initialize();

        // Register builtin commands.
        $this->addCommands(array(
          new InitCommand(),
          new ConfigCommand()
        ));

        // Initialize aplication parameters.
        $this->homeConfigurationPath = getenv("HOME")."/.altax/config.php";
        $this->defaultConfigurationPath = getcwd()."/.altax/config.php";
    }
    
    /**
     * Override doRun method for altax preprocess.
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->loadConfiguration($input, $output);
        $this->registerTasks();

        // Switch debug mode
        if (true === $input->hasParameterOption(array('--debug', '-d'))) {
            $this->getContext()->set("debug", true);
        }

        return parent::doRun($input, $output);
    }

    /**
     * Load configuration files.
     */
    public function loadConfiguration(InputInterface $input, OutputInterface $output)
    {
        if ($this->loadedConfiguration === true) {
            return;
        }
        
        $context = $this->getContext();

        // Load configuration.
        // At first, load user home setting.
        $configurationPath = $this->getHomeConfigurationPath();
        if (is_file($configurationPath)) {
            include_once $configurationPath;
            $configs = $context->get('configs');
            $configs[] = $configurationPath;
            $context->set('configs', $configs);
        }

        // At second, load current working directory setting.
        $configurationPath = $this->getDefaultConfigurationPath();
        if (is_file($configurationPath)) {
            include_once $configurationPath;
            $configs = $context->get('configs');
            $configs[] = $configurationPath;
            $context->set('configs', $configs);
        }

        // At third, load specified file by a option.
        if (true === $input->hasParameterOption(array('--file', '-f'))) {
            $configurationPath = $input->getParameterOption(array('--file', '-f'));
            if ($configurationPath && is_file($configurationPath)) {
                include_once $configurationPath;
                $configs = $context->get('configs');
                $configs[] = $configurationPath;
                $context->set('configs', $configs);
            } else if ($configurationPath) {
                throw new \RuntimeException("$configurationPath not found");
            }
        }
        
        $classes = get_declared_classes();
        print_r($classes);
        

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


