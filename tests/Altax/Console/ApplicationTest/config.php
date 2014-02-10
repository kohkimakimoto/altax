<?php

require_once __DIR__."/Test01Command.php";

Server::node("127.0.0.1");
Server::node("localhost", array("host" => "127.0.0.1", "username" => getenv("USER"), "port" => 22, "key" => getenv("HOME")."/.ssh/id_rsa"));

Server::role("test", array("127.0.0.1", "localhost"));

// Basic test task
Task::register("testBasic", function($task){

    $task->writeln("output log");
    $task->process("echo runLocally")->runLocally();
    
    // Can't catch the output message on PHPUnit test case.
    // Because this process runs on child forked process.
    $task->process("echo run Remotely")
        ->to("127.0.0.1")
        ->run()
        ;

    $task->process(function($node){

        return "echo ".$node->getName()."";

    })->to("127.0.0.1")->runLocally();

    $task->process(function($node){

        return "echo ".$node->getName()."";

    })->to("127.0.0.1")->run();

    $task->process('echo {{ $node->getName() }}')->to("127.0.0.1")->runLocally();

    $task->run("echo aaaa", array("127.0.0.1"));
    $task->runLocally("echo aaaa", array("127.0.0.1"));
    $task->runLocally("echo aaaa");

    $task->process("echo hello")
        ->to("127.0.0.1", "localhost")
        ->cwd(getenv("HOME"))
        ->run();

    $task->process("echo hello")
        ->to("test")
        ->run();

    $task->call("testHidden");
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
