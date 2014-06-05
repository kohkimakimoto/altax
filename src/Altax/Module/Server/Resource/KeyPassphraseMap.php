<?php
namespace Altax\Module\Server\Resource;

/**
 * KeyPassphraseMap is a mapping table SSH key path and passphrase.
 */
class KeyPassphraseMap
{
	private static $instance = null;

	protected $map = array();

	public static function getSharedInstance()
	{
		if (!self::$instance) {
			self::$instance = new KeyPassphraseMap();
		}
		return self::$instance;
	}

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
