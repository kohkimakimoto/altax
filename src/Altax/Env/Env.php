<?php
namespace Altax\Env;

class Env
{
    protected $parameters = array();

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

    public function parameters()
    {
        return $this->parameters;
    }
}
