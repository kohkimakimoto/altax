<?php
/*
 | -------------------------------------------------------------
 | This class is referenced `Illuminate\Support\Str`
 | that is a part of laravel framework.
 | 
 | see https://github.com/laravel/framework
 | 
 | The Laravel framework is open-sourced software licensed 
 | under the MIT license.
 | -------------------------------------------------------------
*/
namespace Altax\Util;

class Str
{
    /**
     * Convert a value to camel case.
     *
     * @param  string  $value
     * @return string
     */
    public static function camel($value)
    {
        return lcfirst(static::studly($value));
    }

    /**
     * Convert a string to snake case.
     *
     * @param  string  $value
     * @param  string  $delimiter
     * @return string
     */
    public static function snake($value, $delimiter = '_')
    {
        $replace = '$1'.$delimiter.'$2';

        return ctype_lower($value) ? $value : strtolower(preg_replace('/(.)([A-Z])/', $replace, $value));
    }

    /**
     * Convert a value to studly caps case.
     *
     * @param  string  $value
     * @return string
     */
    public static function studly($value)
    {
        $value = ucwords(str_replace(array('-', '_'), ' ', $value));

        return str_replace(' ', '', $value);
    }

}