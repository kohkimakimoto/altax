<?php

/*
 | -----------------------------------------------------
 | Boot process of altax is to set container instance up.
 | ----------------------------------------------------- 
 */
$container = new \Altax\Foundation\Container();

// Determine Loaded configuration files.
$container->setConfigFiles(array(
    "home" => getenv("HOME")."/.altax/config.php",
    "current", getcwd()."/.altax/config.php",
    ));

$container->setModules(array(
    'Task' => 'Altax\Module\Task\Task',
    'Role' => 'Altax\Module\Role\Role',
    'Node' => 'Altax\Module\Node\Node',
    ));

//$container->setAliases(array(
//    'Task' => 'Altax\Module\Task',
//    ));


return $container;

