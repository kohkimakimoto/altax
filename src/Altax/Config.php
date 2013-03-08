<?php
/**
 * Altax_Config is a class representing Altax Global Configurations.
 *
 * @author kohkimakimoto <kohki.makimoto@gmail.com>
 * @version $Revision$
 */
class Altax_Config
{
  /**
   * Default configuration file path.
   */
  const DEFAULT_CONFIG = 'altax.php';

  /**
   * Array of configuration values.
   * @var unknown
   */
  protected static $config = array();

  public static function init($path = null)
  {
    if (empty($path)) {
      $path = self::DEFAULT_CONFIG;
    }

    // default
    Altax_Config::set('tasks', array());
    Altax_Config::set('hosts', array());
    Altax_Config::set('roles', array());

    if (file_exists($path)) {
      include_once($path);
    }
  }

  /**
   * Load configurations from a file.
   * @param unknown $path
   */
  protected static function loadConfig($path)
  {
    if (empty($path)) {
      $path = self::DEFAULT_CONFIG;
    }

    if (!file_exists($path)) {
      throw new Altax_Exception("Configuration file is not found.");
    }

    return include($path);
  }

  /**
   * Get a config parameter.
   * @param unknown $name
   * @param string $default
   * @return Ambigous <string, unknown, multitype:>
   */
  public static function get($name, $default = null, $delimiter = '/')
  {
    $config = self::$config;
    foreach (explode($delimiter, $name) as $key) {
      $config = isset($config[$key]) ? $config[$key] : $default;
    }
    return $config;
  }

  /**
   * Set a config parameter.
   * @param unknown $name
   * @param unknown $value
   */
  public static function set($name, $value)
  {
    self::$config[$name] = $value;
  }

  public static function delete($name)
  {
    unset(self::$config[$name]);
  }

  /**
   * Get All config parameters.
   * @return multitype:
   */
  public static function getAll()
  {
    return self::$config;
  }

  public static function getAllOnFlatArray($namespace = null, $key = null, $array = null, $delimiter = '/')
  {
    $ret = array();

    if ($array === null) {
      $array = self::$config;
    }

    foreach ($array as $key => $val) {
      if (is_array($val) && $val) {
        if ($namespace === null) {
          $ret = array_merge($ret, self::getAllOnFlatArray($key, $key, $val, $delimiter));
        } else {
          $ret = array_merge($ret, self::getAllOnFlatArray($namespace.$delimiter.$key, $key, $val, $delimiter));
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
