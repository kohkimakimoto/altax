<?php
namespace Altax\Server;

class ServerManager
{
    protected $nodes = array();

    protected $roles = array();

    protected $keyPassphraseMap;

    protected $env;

    public function __construct($keyPassphraseMap, $env)
    {
        $this->keyPassphraseMap = $keyPassphraseMap;
        $this->env = $env;
    }

    public function node()
    {
        $args = func_get_args();
        if (count($args) === 0) {
            throw new \InvalidArgumentException("Missing argument. Must 1 arguments at minimum.");
        }

        $node = new Node($args[0], $this->keyPassphraseMap, $this->env);

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
            $node = $this->getNode($nodeName, new Node($nodeName, $this->keyPassphraseMap, $this->env));
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

    public function makeNode($name)
    {
        return new Node($name, $this->keyPassphraseMap, $this->env);
    }

    /**
     * Load nodes by the conditions.
     * @param  array $conditions
     * @return array
     */
    public function findNodes(array $conditions)
    {
        $candidateNodeNames = array();
        $concreteNodes = array();

        if (is_vector($conditions)) {
            foreach ($conditions as $condition) {
                if (is_string($condition)) {
                    $candidateNodeNames[] = array(
                        "type" => null, // Means both node and role.
                        "name" => $condition,
                        );
                }
            }
        } else {
            foreach ($conditions as $key => $value) {
                if ($key == "nodes" || $key == "node") {
                    $nodes = array();
                    if (is_string($value)) {
                        $nodes[] = $value;
                    } elseif (is_array($value)) {
                        $nodes = $value;
                    }
                    foreach ($nodes as $node) {
                        $candidateNodeNames[] = array(
                            "type" => "node",
                            "name" => $node,
                        );
                    }
                }
                if ($key == "roles" || $key == "role") {
                    $roles = array();
                    if (is_string($value)) {
                        $roles[] = $value;
                    } elseif (is_array($value)) {
                        $roles = $value;
                    }
                    foreach ($roles as $role) {
                        $candidateNodeNames[] = array(
                            "type" => "role",
                            "name" => $role,
                        );
                    }
                }
            }
        }

        foreach ($candidateNodeNames as $candidateNodeName) {

            $node = null;
            $role = null;

            if ($candidateNodeName["type"] === null || $candidateNodeName["type"] == "node") {
                $node = $this->getNode($candidateNodeName["name"]);
            }

            if ($candidateNodeName["type"] === null || $candidateNodeName["type"] == "role") {
                $role = $this->getRole($candidateNodeName["name"]);
            }

            if ($node && $role) {
                throw new \RuntimeException("The key '".$candidateNodeName["name"]."' was found in both nodes and roles. So It couldn't identify to unique node.");
            }

            if (!$node && !$role && ($candidateNodeName["type"] === null || $candidateNodeName["type"] == "node")) {
                // Passed unregistered node name. Create node instance.
                $node = $this->makeNode($candidateNodeName["name"]);
            }

            if ($node) {
                $concreteNodes[$node->getName()] = $node;
            }

            if ($role) {
                foreach ($role->getNodes() as $nodeName => $node) {
                    $concreteNodes[$nodeName] = $node;
                }
            }
        }

        return $concreteNodes;
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

    public function getKeyPassphraseMap()
    {
        return $this->keyPassphraseMap;
    }
}
