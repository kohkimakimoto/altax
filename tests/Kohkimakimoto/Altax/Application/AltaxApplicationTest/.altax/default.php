<?php
/**
 * Altax Configurations.
 *
 * You need to modify this file for your environment.
 *
 * @see https://github.com/kohkimakimoto/altax
 * @author yourname <youremail@yourcompany.com>
 */

//
// Host and role configurations.
//
role('web', '127.0.0.1');
role('web', array('192.168.0.1', '192.168.0.2'));

set("param1", "aaaaaaaaaa");
get("param1");

desc('This is a sample task.');
task('sample',array('roles' => 'web'), function($host, $args){

  run('echo Hellow World!');

});

