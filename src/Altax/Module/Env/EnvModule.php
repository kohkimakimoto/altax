<?php
namespace Altax\Module\Env;

use Altax\Foundation\Module;

/**
 * Env module 
 */
class EnvModule extends Module
{   
    protected $vars = array();

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