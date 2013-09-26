<?php
namespace Kohkimakimoto\Altax\Application;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputOption;
use Kohkimakimoto\Altax\Command\InitCommand; 
use Kohkimakimoto\Altax\Command\ConfigCommand; 
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

    protected $baseDir = null;
    protected $homeConfigurationPath = null;
    protected $defaultConfigurationPath = null;

    public function __construct($name = "Altax", $version = self::VERSION)
    {
        parent::__construct($name, $version);

        // Initilize Context of this application.
        Context::createInstance();

        // Register builtin commands.
        $this->addCommands(array(
          new InitCommand(),
          new ConfigCommand()
        ));

        // Initialize aplication parameters.
        $this->homeConfigurationPath = getenv("HOME")."/.altax/altax.php";
        $this->defaultConfigurationPath = getcwd()."/.altax/altax.php";
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


