<?php
set("hoge", "foo");
get("hoge");

$configuration = new \Kohkimakimoto\Altax\Util\Configuration();
$configuration->loadHosts(array(__DIR__."/hosts.php"));

role('localhost', '127.0.0.1');
role('web', array('192.168.0.1', '192.168.0.2'));



