<?php
namespace Altax\Server;

class Server
{
    protected $nodes = array();

    protected $roles = array();

    public function __construct()
    {
    }

    public function nodes()
    {
        return $this->nodes;
    }

    public function node()
    {
        $args = func_get_args();
        if (count($args) < 1) {
            throw new \InvalidArgumentException("Missing argument. Must 1 arguments at minimum.");
        }
        $node = new Node();
        if (count($args) === 1) {
            // When it's passed 1 argument, register node with name only.
            $node->setName($args[0]);

        } elseif (count($args) === 2) {
            // When it's passed 2 arguments, register node with roles and some options.
            $node->setName($args[0]);

            if (is_string($args[1]) || is_vector($args[1])) {
                $node->setRoles($args[1]);
            } else {
                if (isset($args[1]['roles'])) {
                    $node->setRoles($args[1]['roles']);
                    unset($args[1]['roles']);
                }
                $node->setOptions($args[1]);
            }
        } else {
            // When it's passed more than 3 arguments, register node with roles and some options.
            $node->setName($args[0]);
            $node->setOptions($args[1]);
            $node->setRoles($args[2]);
        }

        $this->nodes[$node->getName()] = $node;

        $roles = $node->roles();
        if ($roles) {
            if (is_string($roles)) {
                $this->role($roles, $node->getName());
            } elseif (is_array($roles)) {
                foreach ($roles as $role) {
                    $this->role($role, $node->getName());
                }
            }
        }

        return $node;
    }

    public function role($role, $nodes)
    {
        if (is_string($nodes)) {
           $nodes = array($nodes);
        }

/*
        foreach ($nodes as $node) {
            //$this->container->set("roles/".$role."/".$node, $node);
            $this->roles[$role]
            $nodeObject = $this->getNode($node);
            if (!$nodeObject) {
                $this->node($node);
                $nodeObject = $this->getNode($node);
            }

            $nodeObject->mergeReferenceRoles($role);
        }
*/
    }

    public function nodesFromSSHConfigHosts()
    {
        $nodesOptions = SSHConfig::parseToNodeOptionsFromFiles(array(
            "/etc/ssh_config",
            "/etc/ssh/ssh_config",
            getenv("HOME")."/.ssh/config",
        ));

        foreach ($nodesOptions as $key => $option) {
            $this->node($key, $option);
        }
    }

    public function getNode($name)
    {
        return $this->container->get("nodes/".$name, null);
    }

    public function getRole($name)
    {
        return $this->container->get("roles/".$name, null);
    }
}
