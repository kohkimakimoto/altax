<?php

Task::register("sample", function(){


});

Task::register("sample2", function(){

    
});

Task::register("sample_hidden", function(){

    
})->hidden();

Task::register("sample3", function(){

    
})->after("sample2")->before("sample");