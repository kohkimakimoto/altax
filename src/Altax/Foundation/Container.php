<?php
namespace Altax\Foundation;

/**
 * Altax application container
 *
 * It's not a DI Container.
 * This class contains core objects used globally.
 */
class Container implements \ArrayAccess, \Iterator, \Countable
{
    /**
     * Name of the application.
     */
    const NAME = "Altax";

    /**
     * Version of the application.
     */
    const VERSION = "3.0.16";

    /**
     * git commit hash.
     */
    const COMMIT = "%commit%";

    /**
     * Configuration file paths to load.
     */
    protected $configFiles = array();

    /**
     * Modules
     */
    protected $modules = array();

    /**
     *
     */
    protected $input;

    /**
     *
     */
    protected $output;

    /**
     * Container managed instances
     */
    protected $instances = array();

    /**
     * Application
     */
    protected $app = null;

    public function getName()
    {
        return self::NAME;
    }

    public function getVersion()
    {
        return self::VERSION;
    }

    public function getVersionWithCommit()
    {
        return self::VERSION." - ".self::COMMIT;
    }

    public function isPhar()
    {
        return !(preg_match("/commit/", self::COMMIT) === 1);
    }

    /**
     * [getConfigFiles description]
     * @return [type] [description]
     */
    public function getConfigFiles()
    {
        return $this->configFiles;
    }

    public function setConfigFiles(array $files)
    {
        $this->configFiles = $files;
    }

    public function setConfigFile($key, $path)
    {
        $this->configFiles[$key] = $path;
    }

    public function getConfigFile($key)
    {
        return isset($this->configFiles[$key]) ? $this->configFiles[$key] : null;
    }

    public function setModules(array $modules)
    {
        $this->modules = $modules;
    }

    public function addModule($name, $module)
    {
        $this->modules[$name] = $module;
    }

    public function getModules()
    {
        return $this->modules;
    }

    public function getModule($name)
    {
        return $this->modules[$name];
    }

    public function setInput($input)
    {
        $this->input = $input;
    }

    public function getInput()
    {
        return $this->input;
    }

    public function setOutput($output)
    {
        $this->output = $output;
    }

    public function getOutput()
    {
        return $this->output;
    }

    public function setApp($app)
    {
        $this->app = $app;
    }

    public function getApp()
    {
        return $this->app;
    }

    /**
     * Get a value
     * @param unknown $key
     */
    public function get($key, $default = null, $delimiter = '/')
    {
        $instances = $this->instances;

        foreach (explode($delimiter, $key) as $k) {
            $instances = isset($instances[$k]) ? $instances[$k] : $default;
        }

        return $instances;
    }

    /**
    * Set a value.
    * @param unknown $key
    * @param unknown $value
    */
    public function set($key, $value, $delimiter = '/')
    {
        if (strpos($key, $delimiter) === false) {
            $this->instances[$key] = $value;
            return $this;
        }

        $instances = $this->instances;

        $keys = explode($delimiter, $key);
        $lastKeyIndex = count($keys) - 1;
        $ref = &$instances;
        foreach (explode($delimiter, $key) as $i => $k) {
            array_shift($keys);
            if (isset($ref[$k])) {

                if ($i === $lastKeyIndex) {
                    // last key
                    $ref[$k] = $value;
                } else {
                    $ref = &$ref[$k];
                }

            } else {
                if (is_array($ref)) {
                    $ref[$k] = $this->convertMultidimensional($keys, $value);
                } else {
                    throw new \RuntimeException("Couldn't set a value");
                }
                break;
            }
        }


        $this->instances = $instances;
        return $this;
    }

    /**
     * Convert one dimensional array into multidimensional array
     */
    protected function convertMultidimensional($oneDimArray, $leafValue)
    {
        $multiDimArray = array();
        $ref = &$multiDimArray;
        foreach ($oneDimArray as $key) {
            $ref[$key] = array();
            $ref = &$ref[$key];
        }
        $ref = $leafValue;

        return $multiDimArray;
    }

    /**
     * Delete a value.
     * @param unknown $key
     */
    public function delete($key)
    {
        unset($this->instances[$key]);
    }

    public function getInstances()
    {
        return $this->instances;
    }

    public function offsetSet($offset, $value) {
        $this->instances[$offset] = $value;
    }

    public function offsetExists($offset) {
        return isset($this->instances[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->instances[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->instances[$offset]) ? $this->instances[$offset] : null;
    }

    public function current() {
        return current($this->instances);
    }

    public function key() {
        return key($this->instances);
    }

    public function next() {
        return next($this->instances);
    }

    public function rewind() {
        reset($this->instances);
    }

    public function valid() {
        return ($this->current() !== false);
    }

     public function count() {
        return count($this->instances);
    }
}
