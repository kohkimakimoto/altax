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
    }

    public function get($key, $default = null)
    {
        return isset($this->parameters[$key]) ? $this->parameters[$key] : $default;
    }

    public function providers($providers)
    {
        foreach ($providers as $provider) {
            with(new $provider($this->application))->register();
        }
    }

    public function aliases(array $aliases = array())
    {
        AliasLoader::getInstance($aliases)->register();
    }

    public function parameters()
    {
        return $this->parameters;
    }
}
