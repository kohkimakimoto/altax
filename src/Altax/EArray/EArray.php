<?php
/*
 * This program was created by Kohki Makimoto <kohki.makimoto@gmail.com>
 */
namespace Altax\EArray;

/**
 * EArray is a PHP Class to provide convenient ways to access a PHP Array.
 *
 * @author Kohki Makimoto <kohki.makimoto@gmail.com>
 */
class EArray implements \ArrayAccess, \Iterator, \Countable
{
    const ORDER_LOW_TO_HIGH = 1;
    const ORDER_HIGHT_TO_LOW = -1;

    protected $array;

    /**
     * Constructor
     * @param Array $array
     */
    public function __construct($array = array())
    {
        if (!is_array($array)) {
            throw new \RuntimeException("You need to pass Array to constructor.");
        }
        $this->array = $array;
    }
    
    /**
     * Get a value
     * @param unknown $key
     */
    public function get($key, $default = null, $delimiter = '/')
    {
        $array = $this->array;

        foreach (explode($delimiter, $key) as $k) {
          $array = isset($array[$k]) ? $array[$k] : $default;
        }

        if (is_array($array)) {
            $array = new EArray($array);
        }

        return $array;
    }

    /**
    * Set a value.
    * @param unknown $key
    * @param unknown $value
    */
    public function set($key, $value)
    {
        $this->array[$key] = $value;
    }

    /**
     * Sort a array.
     * @param  String $key
     * @param  String $delimiter
     * @return EArray $earray
     */
    public function sort($key = null, $delimiter = '/')
    {
        return $this->doSort($key, $delimiter, self::ORDER_LOW_TO_HIGH);
    }

    /**
     * Reverse sort a array.
     * @param  String $key
     * @param  String $delimiter
     * @return EArray $earray
     */
    public function rsort($key = null, $delimiter = '/')
    {
        return $this->doSort($key, $delimiter, self::ORDER_HIGHT_TO_LOW);
    }

    protected function doSort($key = null, $delimiter = '/', $order = 1)
    {
        uasort($this->array, function($one, $another) use ($key, $delimiter, $order) {

            $oneValue = null;
            if (is_array($one)) {
                $one = new EArray($one);
                $oneValue = $one->get($key, 0, $delimiter);
            } else {
                $oneValue = $one;
            }

            $anotherValue = null;
            if (is_array($another)) {
                $another = new EArray($another);
                $anotherValue = $another->get($key, 0, $delimiter);
            } else {
                $anotherValue = $another;
            }

            $cmp = 0;
            if (is_numeric($oneValue) && is_numeric($anotherValue)) {
                $oneValue = floatval($oneValue);
                $anotherValue = floatval($anotherValue);
                if ($oneValue == $anotherValue) {
                    $cmp = 0;
                } else {
                    $cmp = ($oneValue < $anotherValue) ? -1 : 1;
                }
            } else {
                $cmp = strcmp($oneValue, $anotherValue);
            }

            if ($order === EArray::ORDER_HIGHT_TO_LOW) {
                $cmp = -$cmp;
            }

            return $cmp;
        });

        return $this;
    }

    /**
     * Delete a value.
     * @param unknown $key
     */
    public function delete($key)
    {
        unset($this->array[$key]);
    }

    /**
    * Get a array.
    * @return array:
    */
    public function toArray()
    {
        return $this->array;
    }

    public function offsetSet($offset, $value) {
        $this->array[$offset] = $value;
    }
    
    public function offsetExists($offset) {
        return isset($this->array[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->array[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->array[$offset]) ? $this->array[$offset] : null;
    }

    public function current() {
        return current($this->array);
    }
    
    public function key() {
        return key($this->array);
    }
    
    public function next() {
        return next($this->array);
    }

    public function rewind() {
        reset($this->array);
    }
    
    public function valid() {
        return ($this->current() !== false);
    }

     public function count() {
        return count($this->array);
    }
}