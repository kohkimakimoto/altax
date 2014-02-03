<?php

namespace Altax\Module\Node\Resource;

class Role
{
    public $name;

    public $nodes = array();
    
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
}