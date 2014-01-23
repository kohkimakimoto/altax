<?php
namespace Altax\Foundation;

/**
 * Altax application container
 */
class Container extends \Kohkimakimoto\EArray\EArray
{
    /**
     * Name of the applicaiton.
     */
    const NAME = "Altax";

    /**
     * Version of the application.
     */
    const VERSION = "3.0.0";

    /**
     * Condfiguration file paths to load.
     */
    protected $configFiles = array();

    /**
     * Aliases of classes.
     */
    protected $aliases = array();

    public function getName()
    {
        return self::NAME;
    }

    public function getVersion()
    {
        return self::VERSION;
    }

    public function getConfigFiles()
    {
        return $this->configFiles;
    }

    public function setConfigFile($key, $path)
    {
        $this->configFiles[$key] = $path;
    }

    public function getConfigFile($key)
    {
        return isset($this->configFiles[$key]) ? $this->configFiles[$key] : null;
    }

    public function setAliases(Array $aliases)
    {
        $this->aliases = $aliases;
    }

    public function getAliases()
    {
        return $this->aliases;
    }
}

