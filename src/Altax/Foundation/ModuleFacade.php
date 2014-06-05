<?php
namespace Altax\Foundation;

/**
 * Altax module facade
 */
abstract class ModuleFacade
{
    /*
    This class is references `Illuminate\Support\Facades\Facade` that is a part of laravel framework.
      
    see https://github.com/laravel/framework

    The MIT License (MIT)

    Copyright (c) <Taylor Otwell>

    Permission is hereby granted, free of charge, to any person obtaining a copy
    of this software and associated documentation files (the "Software"), to deal
    in the Software without restriction, including without limitation the rights
    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the Software is
    furnished to do so, subject to the following conditions:

    The above copyright notice and this permission notice shall be included in
    all copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
    THE SOFTWARE.
    */

    protected static $container;

    protected static $resolvedInstance;

    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    public static function getModuleName()
    {
//        return end(explode("\\", get_called_class()));
        throw new \RuntimeException("ModuleFacade does not implement getModuleName method.");
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

        return static::$resolvedInstance[$name] = static::$container->getModule($name);
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
        $instance = static::resolveModuleInstance(static::getModuleName());

        switch (count($args))
        {
            case 0:
                return $instance->$method();

            case 1:
                return $instance->$method($args[0]);

            case 2:
                return $instance->$method($args[0], $args[1]);

//            case 3:
//                return $instance->$method($args[0], $args[1], $args[2]);
//
//            case 4:
//                return $instance->$method($args[0], $args[1], $args[2], $args[3]);
//
            default:
                return call_user_func_array(array($instance, $method), $args);
        }
    }
}
