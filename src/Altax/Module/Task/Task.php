<?php

namespace Altax\Module\Task;

class Task
{
    public $name;

    public $closure;

    public $description;

    protected $input;

    protected $output;

    public function description($description)
    {
        $this->description = $description;
    }

    public function hasClosure()
    {
        return isset($this->closure);
    }

    public function setInput($input)
    {
        $this->input = $input;
    }

    public function getInput()
    {
        return $this->input;
    }

    public function setOutput($output)
    {
        $this->output = $output;
    }

    public function getOutput()
    {
        return $this->output;
    }

    public function run()
    {
        return $this;
    }
}