<?php
namespace Altax\Server;

class Server
{
    protected $nodes = array();

    protected $roles = array();

    public function __construct()
    {
    }

    public function getNodes()
    {
        return $this->nodes;
    }

    public function getNode($name, $default = null)
    {
        return isset($this->nodes[$name]) ? $this->nodes[$name] : $default;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getRole($name, $default = null)
    {
        return isset($this->roles[$name]) ? $this->roles[$name] : $default;
    }

    public function node()
    {
        $args = func_get_args();
        if (count($args) < 1) {
            throw new \InvalidArgumentException("Missing argument. Must 1 arguments at minimum.");
        }
        $node = new Node($args[0]);
        if (count($args) === 1) {
            // When it's passed 1 argument, register node with name only.

        } elseif (count($args) === 2) {
            // When it's passed 2 arguments, register node with roles and some options.
            if (is_string($args[1]) || is_vector($args[1])) {
                $roleNames = $args[1];
                if (is_string($roleNames)) {
                    $roleNames = array($roleNames);
                }

                foreach ($roleNames as $roleName) {
                    $role = $this->getRole($roleName, new Role($roleName));
                    $role->setNode($node);
                    $node->setRole($role);
                }
            } else {
                if (isset($args[1]['roles'])) {
                    $roleNames = $args[1]['roles'];
                    if (is_string($roleNames)) {
                        $roleNames = array($roleNames);
                    }

                    foreach ($roleNames as $roleName) {
                        $role = $this->getRole($roleName, new Role($roleName));
                        $role->setNode($node);
                        $node->setRole($role);
                    }

                    unset($args[1]['roles']);
                }
                $node->setOptions($args[1]);
            }
        } else {
            // When it's passed more than 3 arguments, register node with roles and some options.
            $node->setOptions($args[1]);

            $roleNames = $args[2];
            if (is_string($roleNames)) {
                $roleNames = array($roleNames);
            }

            foreach ($roleNames as $roleName) {
                $role = $this->getRole($roleName, new Role($roleName));
                $role->setNode($node);
                $node->setRole($role);
            }
        }

        $this->nodes[$node->getName()] = $node;

        // Overide roles
        $roles = $node->getRoles();
        foreach ($roles as $role) {
            $this->roles[$role->getName()] = $role;
        }

        return $node;
    }

    public function role($roleName, $nodeNames)
    {
        if (is_string($nodeNames)) {
           $nodeNames = array($nodeNames);
        }

        $role = $this->getRole($roleName, new Role($roleName));
        foreach ($nodeNames as $nodeName) {
            $node = $this->getNode($nodeName, new Node($nodeName));
            $node->setRole($role);
            $role->setNode($node);

            $this->nodes[$node->getName()] = $node;
        }
        $this->roles[$role->getName()] = $role;
    }

    public function nodesFromSSHConfigHosts()
    {
        $nodesOptions = SSHConfigParser::parseToNodeOptionsFromFiles(array(
            "/etc/ssh_config",
            "/etc/ssh/ssh_config",
            getenv("HOME")."/.ssh/config",
        ));

        foreach ($nodesOptions as $key => $option) {
            $this->node($key, $option);
        }
    }
}
