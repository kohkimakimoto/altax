<?php
namespace Altax\Process;

class NodeProcess extends Process
{
    protected $node;

    public function __construct($node)
    {
        parent::__construct($node->getName());
        $this->node = $node;
    }

    public function getNodeInfo()
    {
        return "<fg=yellow> on </fg=yellow><fg=yellow;options=bold>".$this->getNode()->getName()."</fg=yellow;options=bold>";
    }

    public function getNode()
    {
        return $this->node;
    }

    public function node()
    {
        return $this->getNode();
    }
}
