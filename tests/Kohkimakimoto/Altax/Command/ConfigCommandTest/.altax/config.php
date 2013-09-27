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

// or

// role('web', array('192.168.0.1', '192.168.0.2'));

// or

// host('192.168.0.1', 'web');
// host('192.168.0.2', 'web');

// or (Specify SSH Configurations) 

// host('192.168.0.2', array('port' => '22', 'login_name' => 'yourname', 'identity_file' => '/home/yourname/.ssh/id_rsa'), 'web');


//
// The Following is sample task definition.
//
desc('This is a sample task.');
task('sample',array('roles' => 'web'), function($host, $args){

  run('echo Hellow World!');

});
