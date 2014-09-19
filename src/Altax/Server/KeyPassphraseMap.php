<?php
namespace Altax\Server;

/**
 * KeyPassphraseMap is a mapping table SSH key path and passphrase.
 */
class KeyPassphraseMap
{
    protected $map = array();

    public function setPassphraseAtKey($keyPath, $passphrase)
    {
        $this->map[realpath($keyPath)] = $passphrase;

        return $this;
    }

    public function getPassphraseAtKey($keyPath)
    {
        if (!array_key_exists(realpath($keyPath), $this->map)) {
            return null;
        }

        return $this->map[realpath($keyPath)];
    }

    public function hasPassphraseAtKey($keyPath)
    {
        if ($this->getPassphraseAtKey($keyPath) === null) {
            return false;
        } else {
            return true;
        }
    }
}
