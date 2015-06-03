<?php

/*
 | -----------------------------------------------------
 | Boot process of altax is to set container instance up.
 | ----------------------------------------------------- 
 */
$container = new \Altax\Foundation\Container();

$homedir = getenv("HOME") ? getenv("HOME") : getenv("USERPROFILE");

// Determine Loaded configuration files.
$container->setConfigFiles(array(
    "home" => $homedir."/.altax/config.php",
    "current", getcwd()."/.altax/config.php",
    ));

return $container;

