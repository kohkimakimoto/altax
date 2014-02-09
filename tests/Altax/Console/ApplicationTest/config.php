<?php

Task::register("sample", function(){


});

Task::register("sample2", function(){

    
});

Task::register("sample_hidden", function(){

    
})->hidden();

Task::register("testBeforeAndAfter", function(){

    
})->after("sample2")->before("sample");