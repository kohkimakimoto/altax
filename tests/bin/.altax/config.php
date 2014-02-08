<?php
Server::node("127.0.0.1", array("web", "db"));


Task::register("test001", function($task){

    $task->writeln("This is a test001");
    
});

Task::register("test002", function($task){

    $task->process("whoami")
        ->to("web")
        ->runLocally();

    $task->process("whoami")
        ->to("127.0.0.1")
        ->run();

    $task->process("whoami")
        ->to("127.0.0.1")
        ->run();

});

