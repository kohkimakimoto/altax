<?php
namespace Altax\Module\Server\Resource;

use Altax\Util\Arr;
use Altax\Util\SSHKey;
use Altax\Module\Env\Facade\Env;
use Altax\Module\Server\Resource\KeyPassphraseMap;

/**
 * Node represents a managed server.
 */
class Node
{
    protected $name;

    protected $host;

    protected $port;

    protected $key;

    protected $defaultKey;

    protected $username;

    protected $defaultUsername;

    protected $useAgent = false;

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
        return $this->port ? $this->port : Env::get("server.port");
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
        return $this->defaultKey ? $this->defaultKey : Env::get("server.key");
    }

    public function getKeyOrDefault()
    {
        $key = $this->key ? $this->key : $this->getDefaultKey();
        if (strpos($key, "~") !== false) {
            // replace ~ to home directory
            $key = preg_replace_callback('/^~(?:\/|$)/', function ($m) {
                return str_replace('~', getenv("HOME"), $m[0]);
            }, $key);

        }

        return $key;
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
        return $this->defaultUsername ? $this->defaultUsername : Env::get("server.username");
    }

    public function getUsernameOrDefault()
    {
        return $this->username ? $this->username : $this->getDefaultUsername();
    }

    public function useAgent()
    {
        return $this->useAgent;
    }

    public function getPassphrase()
    {
        return KeyPassphraseMap::getSharedInstance()->getPassphraseAtKey($this->getKeyOrDefault());
    }

    public function isUsedWithPassphrase()
    {
        return SSHKey::hasPassphrase($this->getKeyContents());
    }

    public function getKeyContents()
    {
        return file_get_contents($this->getKeyOrDefault());
    }

    public function setOptions($options)
    {   
        if (!is_array($options)) {
            throw new \RuntimeException("You must pass option as Array");
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

        if (isset($options["agent"])) {
            $this->useAgent = (bool)$options["agent"];
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
