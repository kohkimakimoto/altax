<?php
namespace Altax\Module\Server;

use Altax\Foundation\Module;
use Altax\Module\Server\Resource\Node;
use Altax\Util\Arr;
use Altax\Util\SSHConfig;

/**
 * Server module 
 */
class ServerModule extends Module
{
    /**
     * Set node
     */
    public function node()
    {
        $args = func_get_args();

        if (count($args) < 1) {
            throw new \RuntimeException("Missing argument. Must 1 arguments at minimum.");
        }

        $node = new Node();

        if (count($args) === 1) {
            // When it's passed 1 argument, register node with name only.
            $node->setName($args[0]);

        } elseif (count($args) === 2) {
            // When it's passed 2 arguments, register node with roles and some options.
            $node->setName($args[0]);

            if (is_string($args[1]) || Arr::isVector($args[1])) {
                $node->setReferenceRoles($args[1]);
            } else {
                if (isset($args[1]['roles'])) {
                    $node->setReferenceRoles($args[1]['roles']);
                    unset($args[1]['roles']);
                }
                $node->setOptions($args[1]);
            }
        } else {
            // When it's passed more than 3 arguments, register node with roles and some options.
            $node->setName($args[0]);
            $node->setOptions($args[1]);
            $node->setReferenceRoles($args[2]);
        }

        $this->container->set("nodes/".$node->getName(), $node);

        $roles = $node->getReferenceRoles();
        if ($roles) {
            if (is_string($roles)) {
                $this->role($roles, $node->getName());
            } else if (is_array($roles)) {
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

        foreach ($nodes as $node) {
            $this->container->set("roles/".$role."/".$node, $node);

            $nodeObject = $this->getNode($node);
            if (!$nodeObject) {
                $this->node($node);
                $nodeObject = $this->getNode($node);
            }

            $nodeObject->mergeReferenceRoles($role);
        }
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
