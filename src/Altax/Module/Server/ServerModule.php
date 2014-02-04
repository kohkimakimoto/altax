<?php

namespace Altax\Module\Server;

use Altax\Foundation\Module;
use Altax\Module\Server\Resource\Server;
use Altax\Util\Arr;

class ServerModule extends Module
{
    /**
     * Set server
     */
    public function node()
    {
        $args = func_get_args();

        if (count($args) < 1) {
            throw new \RuntimeException("Missing argument. Must 1 arguments at minimum.");
        }

        $server = new Server();

        if (count($args) === 1) {
            // When it's passed 1 argument, register server with name only.
            $server->setName($args[0]);

        } elseif (count($args) === 2) {
            // When it's passed 2 arguments, register server with roles and some options.
            $server->setName($args[0]);

            if (is_string($args[1]) || Arr::isVector($args[1])) {
                $server->setReferenceRoles($args[1]);
            } else {
                if (isset($args[1]['roles'])) {
                    $server->setRoles($args[1]['roles']);
                    unset($args[1]['roles']);
                }
                $server->setOptions($args[1]);
            }
        } else {
            // When it's passed more than 3 arguments, register server with roles and some options.
            $server->setName($args[0]);
            $server->setOptions($args[1]);
            $server->setReferenceRoles($args[2]);
        }

        $this->container->set("nodes/".$server->getName(), $server);

        $roles = $server->getReferenceRoles();
        if ($roles) {
            if (is_string($roles)) {
                self::role($roles, $server->getName());
            } else if (is_array($roles)) {
                foreach ($roles as $role) {
                    self::role($role, $server->getName());
                }
            }
        }

        return $server;
    }

    public function role($role, $servers)
    {
        if (is_string($servers)) {
           $servers = array($servers);
        }

        foreach ($servers as $server) {
            $this->container->set("roles/".$role."/".$server, $server);
        }

    }

}