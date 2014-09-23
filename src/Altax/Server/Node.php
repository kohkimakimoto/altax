<?php
namespace Altax\Server;

/**
 * Node represents a managed server.
 */
class Node
{
    protected $name;

    protected $host;

    protected $port;

    protected $key;

    protected $roles = array();

    protected $defaultKey;

    protected $username;

    protected $defaultUsername;

    protected $useAgent = false;

    protected $keyPassphraseMap;

    protected $env;

    public function __construct($name, $keyPassphraseMap, $env)
    {
        $this->name = $name;
        $this->keyPassphraseMap = $keyPassphraseMap;
        $this->env = $env;
    }

    public function __toString()
    {
        return $this->name;
    }

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
        return $this->port ? $this->port : $this->env->get("server.port");
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
        return $this->defaultKey ? $this->defaultKey : $this->env->get("server.key");
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
        return $this->defaultUsername ? $this->defaultUsername : $this->env->get("server.username");
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
        return $this->keyPassphraseMap->getPassphraseAtKey($this->getKeyOrDefault());
    }

    public function isUsedWithPassphrase()
    {
        return SSHKey::hasPassphrase($this->getKeyContents());
    }

    public function getKeyContents()
    {
        return file_get_contents($this->getKeyOrDefault());
    }

    public function setOptions(array $options)
    {
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
            $this->useAgent = (bool) $options["agent"];
        }
    }

    public function setRole(Role $role)
    {
        $this->roles[$role->getName()] = $role;
    }

    public function getRole($name)
    {
        return $this->roles[$name];
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function roles()
    {
        return $this->roles;
    }

    public function getSSHConnection()
    {
        $ssh = new \Net_SSH2(
            $this->getHostOrDefault(),
            $this->getPortOrDefault());

        $key = new \Crypt_RSA();

        if ($this->useAgent()) {
            // use ssh-agent
            if (class_exists('System_SSH_Agent', true) == false) {
                require_once 'System/SSH_Agent.php';
            }
            $key = new \System_SSH_Agent();
        } else {
            // use ssh key file
            if ($this->isUsedWithPassphrase()) {
                // use passphrase
                $key->setPassword($this->getPassphrase());
            }

            if (!$key->loadKey($this->getKeyContents())) {
                throw new \RuntimeException('Unable to load SSH key file: '.$this->getKeyOrDefault());
            }
        }

        // login
        if (!$ssh->login($this->getUsernameOrDefault(), $key)) {
            $err = error_get_last();
            $emessage = isset($err['message']) ? $err['message'] : "";
            throw new \RuntimeException('Unable to login '.$this->getName().". ".$emessage);
        }

        return $ssh;
    }

    public function getSFTPConnection()
    {
        $sftp = new \Net_SFTP(
            $this->getHostOrDefault(),
            $this->getPortOrDefault());

        $key = new \Crypt_RSA();
        if ($this->useAgent()) {
            // use ssh-agent
            if (class_exists('System_SSH_Agent', true) == false) {
                require_once 'System/SSH_Agent.php';
            }
            $key = new \System_SSH_Agent();
        } else {
            // use ssh key file
            if ($this->isUsedWithPassphrase()) {
                // use passphrase
                $key->setPassword($this->getPassphrase());
            }

            if (!$key->loadKey($this->getKeyContents())) {
                throw new \RuntimeException('Unable to load SSH key file: '.$this->getKeyOrDefault());
            }
        }

        // login
        if (!$sftp->login($this->getUsernameOrDefault(), $key)) {
            $err = error_get_last();
            $emessage = isset($err['message']) ? $err['message'] : "";
            throw new \RuntimeException('Unable to login '.$this->getName().". ".$emessage);
        }

        return $sftp;
    }
}
