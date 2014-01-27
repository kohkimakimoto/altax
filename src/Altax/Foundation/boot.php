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

return $container;

