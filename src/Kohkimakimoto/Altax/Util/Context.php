<?php
namespace Kohkimakimoto\Altax\Util;

use Kohkimakimoto\Altax\Functions\Builtin;

/**
 * Altax Context Container
 * @author Kohki Makimoto <kohki.makimoto@gmail.com>
 */
class Context
{
    protected static $instance;
    
    protected $parameters = array();

    public static function initialize($configPath)
    {
        self::$instance = new Context();

        require_once __DIR__."/../Functions/builtin.php";
        include_once $configPath;

        return self::$instance;
    }

    public static function getInstance()
    {
        return self::$instance;
    }

    public function __construct()
    {
        $this->set("tasks", array());
        $this->set("hosts", array());
        $this->set("roles", array());
    }

    /**
     * Get a parameter
     */
    public function get($name, $default = null, $delimiter = '/')
    {
        $parameters = $this->parameters;

        foreach (explode($delimiter, $name) as $key) {
          $parameters = isset($parameters[$key]) ? $parameters[$key] : $default;
        }
        return $parameters;
    }

    /**
    * Set a parameter.
    * @param unknown $name
    * @param unknown $value
    */
    public function set($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    public function delete($name)
    {
        unset($this->parameters[$name]);
    }

    /**
    * Get parameters.
    * @return multitype:
    */
    public function getParamters()
    {
        return $this->parameters;
    }

    public function getParametersFlatArray($namespace = null, $key = null, $array = null, $delimiter = '/')
    {
        $ret = array();

        if ($array === null) {
            $array = $this->parameters;
        }

        foreach ($array as $key => $val) {
            if (is_array($val) && $val) {
                if ($namespace === null) {
                    $ret = array_merge($ret, $this->getParametersFlatArray($key, $key, $val, $delimiter));
                } else {
                    $ret = array_merge($ret, $this->getParametersFlatArray($namespace.$delimiter.$key, $key, $val, $delimiter));
                }
            } else {
                if ($namespace !== null) {
                    $ret[$namespace.$delimiter.$key] = $val;
                } else {
                    $ret[$key] = $val;
                }
            }
        }
        return $ret;
    }
}