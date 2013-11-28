<?php
set("hoge", "foo");
get("hoge");

$configuration = new \Kohkimakimoto\Altax\Util\Configuration();
$configuration->loadHosts(array(__DIR__."/hosts.php"));

role('localhost', 'localhost');
role('web', array('192.168.0.1', '192.168.0.2'));



