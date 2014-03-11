<?php
namespace Altax\Module\Env;

use Altax\Foundation\Module;

/**
 * Env module 
 */
class EnvModule extends Module
{

    public function set($key, $value)
    {
        $this->container->set("env/".$key, $value);
    }
    
    public function get($key, $default = null)
    {
        return $this->container->get("env/".$key, $default);
    }
}