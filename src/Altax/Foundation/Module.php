<?php
namespace Altax\Foundation;

/**
 * Altax module class
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
