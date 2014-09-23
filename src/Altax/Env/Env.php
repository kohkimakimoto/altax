<?php
namespace Altax\Env;

class Env
{
    protected $parameters = array();

    public function __construct()
    {
        // Default values.
        $this->set("server.port", 22);
        $this->set("server.key", getenv("HOME")."/.ssh/id_rsa");
        $this->set("server.username", getenv("USER"));
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
