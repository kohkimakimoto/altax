<?php

$app = new \Altax\Application\Application();

// Determine Loaded configuration files.
$app->setConfigFile("home", getenv("HOME")."/.altax/config.php");
$app->setConfigFile("current", getcwd()."/.altax/config.php");

return $app;

