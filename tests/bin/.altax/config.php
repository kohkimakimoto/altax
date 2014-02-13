<?php
Server::node("127.0.0.1", array("web", "db"));


Task::register("test001", function($task){

    $task->writeln("This is a test001");
    
});

Task::register("test002", function($task){

});

