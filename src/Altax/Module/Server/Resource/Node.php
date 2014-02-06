<?php
namespace Altax\Module\Server\Resource;

use Altax\Util\Arr;

class Node
{
    protected $name;

    protected $host;

    protected $port;

    protected $key;

    protected $defaultKey;

    protected $username;

    protected $defaultUsername;

    protected $referenceRoles = array();

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setHost($host)
    {
        $this->host = $host;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getHostOrDefault()
    {
        return $this->host ? $this->host : $this->name;
    }

    public function setPort($port)
    {
        $this->port = $port;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getPortOrDefault()
    {
        return $this->port ? $this->port : 22;
    }

    public function setKey($key)
    {
        $this->key = $key;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setDefaultKey($defaultKey)
    {
        $this->defaultKey = $defaultKey;
    }

    public function getDefaultKey()
    {
        return $this->defaultKey ? $this->defaultKey : getenv("HOME")."/.ssh/id_rsa";
    }

    public function getKeyOrDefault()
    {
        return $this->key ? $this->key : $this->getDefaultKey();
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setDefaultUsername($defaultUsername)
    {
        $this->defaultUsername = $defaultUsername;
    }
    
    public function getDefaultUsername()
    {
        return $this->defaultUsername ? $this->defaultUsername : getenv("USER");
    }

    public function getUsernameOrDefault()
    {
        return $this->username ? $this->username : $this->getDefaultUsername();
    }

    public function setOptions($options)
    {   
        if (!is_array($options)) {
            throw new RuntimeException("You must pass option as Array");
        }

        if (isset($options["host"])) {
            $this->host = $options["host"];
        }

        if (isset($options["port"])) {
            $this->port = $options["port"];
        }

        if (isset($options["key"])) {
            $this->key = $options["key"];
        }

        if (isset($options["username"])) {
            $this->username = $options["username"];
        }
    }

    public function setReferenceRoles($roles)
    {
        if (is_string($roles)) {
            $roles = array($roles => $roles);
        } elseif (Arr::isVector($roles)) {
            $vrs = $roles;
            $roles = array();
            foreach ($vrs as $r) {
                $roles[$r] = $r;
            }
        }

        $this->referenceRoles = $roles;
    }

    public function getReferenceRoles()
    {
        return $this->referenceRoles;
    }

    public function mergeReferenceRoles($roles)
    {
        if (is_string($roles)) {
            $roles = array($roles => $roles);
        }
        $this->referenceRoles = array_merge($this->referenceRoles, $roles);
    }
}