<?php
namespace Altax\Foundation;

/*
 | -------------------------------------------------------------
 | This class is referenced `Illuminate\Support\Facades\Facade`
 | that is a part of laravel framework.
 | 
 | see https://github.com/laravel/framework
 | 
 | The Laravel framework is open-sourced software licensed 
 | under the MIT license.
 | -------------------------------------------------------------
*/

/**
 * Altax module
 */
abstract class Module
{
    protected static $container;

    protected static $resolvedInstance;


    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getModuleAccessor()
    {
        throw new \RuntimeException("Module does not implement getModuleAccessor method.");
    }

    /**
     * Get the application instance behind the facade.
     *
     * @return \Illuminate\Foundation\Application
     */
    public static function getContainer()
    {
        return static::$container;
    }

    /**
     * Set the application instance.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public static function setContainer($container)
    {
        static::$container = $container;
    }

    /**
     * Clear a resolved facade instance.
     *
     * @param  string  $name
     * @return void
     */
    public static function clearResolvedInstance($name)
    {
        unset(static::$resolvedInstance[$name]);
    }
    
    /**
     * Clear all of the resolved instances.
     *
     * @return void
     */
    public static function clearResolvedInstances()
    {
        static::$resolvedInstance = array();
    }

    /**
     * Resolve the facade root instance from the container.
     *
     * @param  string  $name
     * @return mixed
     */
    protected static function resolveModuleInstance($name)
    {
        if (is_object($name)) return $name;

        if (isset(static::$resolvedInstance[$name]))
        {
            return static::$resolvedInstance[$name];
        }

        return static::$resolvedInstance[$name] = static::$container->get($name);
    }


    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string  $method
     * @param  array   $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::resolveModuleInstance(static::getModuleAccessor());

        switch (count($args))
        {
            case 0:
                return $instance->$method();

            case 1:
                return $instance->$method($args[0]);

            case 2:
                return $instance->$method($args[0], $args[1]);

            case 3:
                return $instance->$method($args[0], $args[1], $args[2]);

            case 4:
                return $instance->$method($args[0], $args[1], $args[2], $args[3]);

            default:
                return call_user_func_array(array($instance, $method), $args);
        }
    }
}