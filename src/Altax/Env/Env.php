<?php
namespace Altax\Env;

class Env
{
    protected $parameters = array();

    public function __construct($configFiles)
    {
        // Default values.
        $this->set("config_files", $configFiles);
        $this->set("server.port", 22);
        $this->set("server.key", getenv("HOME")."/.ssh/id_rsa");
        $this->set("server.username", getenv("USER"));
        $this->set("command.shell", "/bin/bash -l -c");

        $scripts = array(realpath(__DIR__."/../Shell/scripts"));
        foreach ($configFiles as $configFile) {
            $scripts[] = dirname($configFile)."/scripts";
        }
        $this->set("script.paths", $scripts);
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
