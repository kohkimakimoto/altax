<?php

/*
 | -----------------------------------------------------
 | Boot process of altax is to set container instance up.
 | ----------------------------------------------------- 
 */
$container = new \Altax\Foundation\Container();

// Determine Loaded configuration files.
$container->setConfigFile("home", getenv("HOME")."/.altax/config.php");
$container->setConfigFile("current", getcwd()."/.altax/config.php");

$container->setAliases(array(
    'Task' => 'Altax\Facades\Task',
    ));

return $container;

