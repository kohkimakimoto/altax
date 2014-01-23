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

$container->setModules(array(
    'Altax\Module\Task',
    ));

$container->setAliases(array(
    'Task' => 'Altax\Module\Task',
    ));


return $container;

