<?php
// Target hosts and ssh connection settings.
host('host001',  array('host' => '127.0.0.1'), 'local');


desc('Test001 task.');
task('test001', array('roles' => 'local'), function($host, $args){


});

task('test002', array('roles' => 'local'), function($host, $args){


});

task('test003', array('roles' => 'local'), function($host, $args){


});

before("test002", "test001");
after("test002", "test003");

task('localrun_test001', function($host, $args){

    echo "localrun!";

});