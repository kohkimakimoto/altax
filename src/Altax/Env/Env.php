<?php
namespace Altax\Env;

use Altax\Foundation\AliasLoader;

class Env
{
    protected $parameters = array();
    protected $application = null;

    public function __construct($application)
    {
        $this->application = $application;
    }

    public function updateFromArray($env = array())
    {
        foreach ($env as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function set($key, $value)
    {
        $this->parameters[$key] = $value;

        if ($key === 'aliases') {
            // Replases aliases.
            AliasLoader::getInstance()->setAliases($value);
        }

        if ($key === 'aliases.prefix') {
            // Replases aliases.
            AliasLoader::getInstance()->setPrefix($value);
        }

        if ($key === 'providers') {
            // Registers providers
            $this->application->registerProviders($value);
        }
    }

    public function get($key, $default = null)
    {
        return isset($this->parameters[$key]) ? $this->parameters[$key] : $default;
    }

    public function parameters()
    {
        return $this->parameters;
    }
}
