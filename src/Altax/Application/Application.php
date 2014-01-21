<?php
namespace Altax\Application;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Altax application container
 */
class Application extends ContainerBuilder
{
    const NAME = "Altax";
    const VERSION = "3.0.0";

    public function boot()
    {

    }
}

