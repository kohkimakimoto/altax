<?php
namespace Altax\Server;

/**
 * Server role.
 */
class Role
{
    public $name;

    public $nodes = array();

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setNode(Node $node)
    {
        $this->nodes[$node->name] = $node;
    }

    public function getNode($name)
    {
        return $this->nodes[$node->name];
    }

    public function getNodes()
    {
        return $this->nodes;
    }

    public function nodes()
    {
        return $this->nodes;
    }

}
