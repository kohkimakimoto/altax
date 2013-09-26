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
    
    protected $attributes = array();

    public static function createInstance()
    {
        self::$instance = new Context();

        // Load buntin functions.
        require_once __DIR__."/../Functions/builtin.php";

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
     * Get a attribute
     */
    public function get($name, $default = null, $delimiter = '/')
    {
        $attributes = $this->attributes;

        foreach (explode($delimiter, $name) as $key) {
          $attributes = isset($attributes[$key]) ? $attributes[$key] : $default;
        }
        return $attributes;
    }

    /**
    * Set a attribute.
    * @param unknown $name
    * @param unknown $value
    */
    public function set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Delete a attribute.
     */
    public function delete($name)
    {
        unset($this->attributes[$name]);
    }

    /**
    * Get attributes.
    * @return multitype:
    */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Get attributes as flat array to show infomations.
     */
    public function getAttributesAsFlatArray($namespace = null, $key = null, $array = null, $delimiter = '/')
    {
        $ret = array();

        if ($array === null) {
            $array = $this->attributes;
        }

        foreach ($array as $key => $val) {
            if (is_array($val) && $val) {
                if ($namespace === null) {
                    $ret = array_merge($ret, $this->getAttributesAsFlatArray($key, $key, $val, $delimiter));
                } else {
                    $ret = array_merge($ret, $this->getAttributesAsFlatArray($namespace.$delimiter.$key, $key, $val, $delimiter));
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