<?php
namespace Altax\Foundation;

// it refers to Illuminate\Foundation\AliasLoader

/**
 * AliasLoader
 */
class AliasLoader
{
    /**
     * The array of class aliases.
     *
     * @var array
     */
    protected $aliases;

    /**
     * The prefix of aliases.
     *
     * @var string
     */
    protected $prefix = "";

    /**
     * Indicates if a loader has been registered.
     *
     * @var bool
     */
    protected $registered = false;

    /**
     * The singleton instance of the loader.
     *
     * @var \Illuminate\Foundation\AliasLoader
     */
    protected static $instance;

    /**
     * Create a new class alias loader instance.
     *
     * @param  array $aliases
     * @return void
     */
    public function __construct(array $aliases = array())
    {
        $this->aliases = $aliases;
    }

    /**
     * Get or create the singleton alias loader instance.
     *
     * @param  array                              $aliases
     * @return \Illuminate\Foundation\AliasLoader
     */
    public static function getInstance(array $aliases = array())
    {
        if (is_null(static::$instance)) static::$instance = new static($aliases);

        $aliases = array_merge(static::$instance->getAliases(), $aliases);

        static::$instance->setAliases($aliases);

        return static::$instance;
    }

    /**
     * Load a class alias if it is registered.
     *
     * @param  string $alias
     * @return void
     */
    public function load($alias)
    {
        if ($this->prefix !== null && $this->prefix !== "") {
            // exists prefix configuration
            if (strpos($alias, $this->prefix) !== 0) {
                return;
            }

            $noprefixAlias = substr($alias, strlen($this->prefix));
            if (isset($this->aliases[$noprefixAlias])) {
                return class_alias($this->aliases[$noprefixAlias], $alias);
            }

            return;
        }

        if (isset($this->aliases[$alias])) {
            return class_alias($this->aliases[$alias], $alias);
        }
    }

    /**
     * Add an alias to the loader.
     *
     * @param  string $class
     * @param  string $alias
     * @return void
     */
    public function alias($class, $alias)
    {
        $this->aliases[$class] = $alias;
    }

    /**
     * Register the loader on the auto-loader stack.
     *
     * @return void
     */
    public function register()
    {
        if (! $this->registered) {
            //$this->prependToLoaderStack();
            $this->apendToLoaderStack();
            $this->registered = true;
        }
    }

    /**
     * Prepend the load method to the auto-loader stack.
     *
     * @return void
     */
    /*
    protected function prependToLoaderStack()
    {
        spl_autoload_register(array($this, 'load'), true, true);
    }
    */

    protected function apendToLoaderStack()
    {
        spl_autoload_register(array($this, 'load'), true, false);
    }

    /**
     * Get the registered aliases.
     *
     * @return array
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * Set the registered aliases.
     *
     * @param  array $aliases
     * @return void
     */
    public function setAliases(array $aliases)
    {
        $this->aliases = $aliases;
    }

    /**
     * Get the registered prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set the registered prefix.
     *
     * @param  string $prefix
     * @return void
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Indicates if the loader has been registered.
     *
     * @return bool
     */
    public function isRegistered()
    {
        return $this->registered;
    }

    /**
     * Set the "registered" state of the loader.
     *
     * @param  bool $value
     * @return void
     */
    public function setRegistered($value)
    {
        $this->registered = $value;
    }

    /**
     * Set the value of the singleton alias loader.
     *
     * @param  \Illuminate\Foundation\AliasLoader $loader
     * @return void
     */
    public static function setInstance($loader)
    {
        static::$instance = $loader;
    }

}
