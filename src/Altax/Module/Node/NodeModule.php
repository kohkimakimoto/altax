<?php

namespace Altax\Module\Node;

use Altax\Foundation\Module;
use Altax\Module\Node\Resource\Node;
use Altax\Util\Arr;

class NodeModule extends Module
{
    /**
     * Set node
     */
    public function host()
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
                    $node->setRoles($args[1]['roles']);
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
        
        return $node;
    }

    public function role()
    {

    }

    /**
     * Register node
     */
    /*
    public function set()
    {
        $node = null;
        $options = array();
        $roles = null;

        $args = func_get_args();
        if (count($args) < 2) {
            throw new \RuntimeException("Missing argument. Must 2 arguments at minimum.");
        }

        if (count($args) === 2) {
            $node = $args[0];
            
            if (is_string($args[1]) || is_vector($args[1])) {
                $roles = $args[1];
            } else {
                if (isset($args[1]['roles'])) {
                    $roles = $args[1]['roles'];
                    unset($args[1]['roles']);
                }
                $options = $args[1];
            }
        } else {
            $node = $args[0];
            $options = $args[1];
            $roles = $args[2];
        }

        $nodes = $this->container->get("nodes", array());
        $nodes[$node] = $options;

        if ($roles) {
            // Register related role
            if (is_string($roles)) {
                $this->container->getModule("Role")->set($roles, $node);
            } else if (is_array($roles)) {
                foreach ($roles as $role) {
                    $this->container->getModule("Role")->set($role, $node);
                }
            }
        }

        $this->container->set("nodes", $nodes);
    }
    */
}