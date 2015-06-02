<?php
namespace Altax\Module\Env;

use Altax\Foundation\Module;

/**
 * Env module 
 */
class EnvModule extends Module
{

    protected $vars = array();

    public function __construct($container)
    {
        parent::__construct($container);

        $homedir = getenv("HOME") ? getenv("HOME") : getenv("USERPROFILE");
        $this->set("homedir", $homedir);

        $username = getenv("USER") ? getenv("USER") : getenv("USERNAME");
        $this->set("username", $username);

        // Default values.
        $this->set("server.port", 22);
        $this->set("server.key", $this->get("homedir")."/.ssh/id_rsa");
        $this->set("server.username", $this->get("username"));
    }

    public function set($key, $value)
    {
        $this->vars[$key] = $value;
    }
    
    public function get($key, $default = null)
    {
        return isset($this->vars[$key]) ? $this->vars[$key] : $default;
    }

    public function getVars()
    {
        return $this->vars;
    }
}
