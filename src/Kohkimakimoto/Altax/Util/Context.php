<?php
namespace Kohkimakimoto\Altax\Util;

/**
 * Altax Context Container
 * @author Kohki Makimoto <kohki.makimoto@gmail.com>
 */
class Context
{
    protected static $instance;
    
    protected $attributes = array();

    protected $parameters = array();

    public static function initialize()
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
        // Default attributes
        $this->set("tasks", array());
        $this->set("hosts", array());
        $this->set("roles", array());
        $this->set("configs", array());
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
     * Get a parameter
     */
    public function getParameter($name, $default = null, $delimiter = '/')
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
    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

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