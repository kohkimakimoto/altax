<?php

require_once __DIR__."/Test01Command.php";

Server::nodesFromSSHConfigHosts();
Server::node("127.0.0.1");
Server::node("localhost", array("host" => "127.0.0.1", "username" => getenv("USER"), "port" => 22, "key" => getenv("HOME")."/.ssh/id_rsa"));
Server::role("test", array("127.0.0.1", "localhost"));

Env::set("server.port", 22);
Env::set("server.key", getenv("HOME")."/.ssh/id_rsa");
Env::set("server.username", getenv("USER"));

// Basic test task
Task::register("testBasic", function($task){


    $task->writeln("output log");

    $task->call("testHidden");
    
    $task->exec(function($process){

        $process->runLocally("echo runLocally!");

    });

    $task->exec(function($process){

        $process->run("echo run!");

    }, array("127.0.0.1"));

    $task->exec(function($process){

        $process->run("echo run!", array("cwd" => "~"));

        $process->getNode();

    }, array("127.0.0.1"));

    $task->exec(function($process){

        $process->run("echo run!");

    });


});


Task::register("testHidden", function($task){
    
    $task->writeln("Run testHidden!");

})->hidden();


Task::register("testBeforeAndAfter0", function($task){

    $task->writeln("before!");

});

Task::register("testBeforeAndAfter2", function($task){

    $task->writeln("after!");

});

Task::register("testBeforeAndAfter1", function($task){

    $task->writeln("hello!");

})
->before("testBeforeAndAfter0")
->after("testBeforeAndAfter2")
;

Task::register("testRegisterCommand", "Test\Altax\Console\ApplicationTest\Test01Command");





// test before after
Task::register("testAncestry1", function($task){


})->before("testAncestry2")->after("testAncestry3");

Task::register("testAncestry2", function($task){

    
})->before("testAncestry2-1");

Task::register("testAncestry2-1", function($task){

    
})->before("testAncestry1");

Task::register("testAncestry3", function($task){
    
})->before("testAncestry1");
