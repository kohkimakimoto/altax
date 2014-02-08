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
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }
}