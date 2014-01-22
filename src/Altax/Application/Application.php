<?php
namespace Altax\Application;

/**
 * Altax application container
 */
class Application extends \Kohkimakimoto\EArray\EArray
{
    const NAME = "Altax";
    const VERSION = "3.0.0";

    protected $configFiles = array();

    public function setConfigFile($key, $path)
    {
        $this->configFiles[$key] = $path;
    }

    public function getConfigFiles()
    {
        return $this->configFiles;
    }

    public function getConfigFile($key)
    {
        return isset($this->configFiles[$key]) ? $this->configFiles[$key] : null;
    }
}

