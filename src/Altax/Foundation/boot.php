<?php

$container = new \Altax\Foundation\Container();

// Determine Loaded configuration files.
$container->setConfigFile("home", getenv("HOME")."/.altax/config.php");
$container->setConfigFile("current", getcwd()."/.altax/config.php");

return $container;

