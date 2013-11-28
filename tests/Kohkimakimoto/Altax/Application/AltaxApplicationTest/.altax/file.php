<?php
desc('This is a sample task.');
task('sample2', array('roles' => 'localhost'), function($host, $args){

    run("echo Hello");

});
