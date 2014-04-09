<?php
namespace Altax\Util;

/**
 * Utility class for ssh key file.
 */
class SSHKey
{
    /**
     * Checking if the keyfile has passphrase. 
     * 
     * see http://superuser.com/questions/201003/checking-ssh-keys-have-passphrases
     * 
     * @param  string  $keyFile SSH key file data
     * @return boolean
     */
    public static function hasPassphrase($keyFile)
    {
        if (preg_match("/Proc-Type.+ENCRYPTED/", $keyFile) === 1) {
            return true;
        } else {
            return false;
        }
    }
}
