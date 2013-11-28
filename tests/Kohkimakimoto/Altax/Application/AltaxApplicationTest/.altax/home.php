<?php
task('sample0', array('roles' => 'localhost'), function($host, $args){

    run("echo Hello");

});


desc('This is a sample task.');
task('sample', array('roles' => 'localhost'), function($host, $args){

    run_task("sample0");
});
