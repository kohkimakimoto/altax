<?php

namespace Altax\Module\Node\Resource;

class Node
{
    public $name;

    public $host;

    public $port;

    public $key;

    public $username;

    public $referenceRoles = array();

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

    public function setPort($port)
    {
        $this->port = $port;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function setKey($key)
    {
        $this->key = $key;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setOptions($options)
    {

    }

    public function setReferenceRoles($roles)
    {
        if (is_string($roles)) {
            $roles = array($roles);
        }

        $this->referenceRoles = $roles;
    }

    public function getReferenceRoles()
    {
        return $this->referenceRoles;
    }



}