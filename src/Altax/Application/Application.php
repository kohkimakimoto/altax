<?php
namespace Altax\Application;

/**
 * Altax application container
 */
class Application extends \Altax\EArray\EArray
{
    const NAME = "Altax";
    const VERSION = "3.0.0";

    protected $configFiles = array();

    public function setConfigFile($key, $path)
    {
        $this->configFiles[$key] = $path;
    }
}

