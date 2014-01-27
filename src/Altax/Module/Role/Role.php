<?php

namespace Altax\Module\Role;

use Altax\Foundation\Module;

class Role extends Module
{
    public function set($role, $nodes)
    {
        if (is_string($nodes)) {
           $nodes = array($nodes);
        }

        $roles = $this->container->get('roles', array());

        foreach ($nodes as $node) {
            if (!isset($roles[$role])) {
                $roles[$role] = array();
            }
            $roles[$role][] = $node;
        }

        $roles[$role] = array_unique($roles[$role]);

        $this->container->set('roles', $roles);
    }
}