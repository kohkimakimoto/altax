<?php
namespace Altax\Util;

/**
 * Utility class for ssh config.
 */
class SSHConfig
{

    // Refering the following code.
    // https://gist.github.com/geeksunny/3376694
    // https://github.com/fitztrev/shuttle
    
    /**
     * parse ssh config
     * @param  [type] $contents [description]
     * @return [type]           [description]
     */
    public static function parse($contents)
    {
        $arr = array();

        $servers = array();

        $lines = explode("\n", $contents);
        foreach ($lines as $line) {

            if (preg_match(
                "/^([ \t#])[ \t]*([^ \t=]+)[ \t=]+(.*)$/", 
                $line,
                $matches)) {

                if ($matches && count($matches) != 4) {
                    continue;
                }

                $isComment = ($matches[1] == "#");
                $first = $matches[2];
                $second = $matches[3];

//                print_r($matches)."\n";

            }
            
        }

        return $arr;
    }
}