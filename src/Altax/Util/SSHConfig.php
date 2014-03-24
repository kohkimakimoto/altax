<?php
namespace Altax\Util;

/**
 * Utility class for ssh config.
 */
class SSHConfig
{
    public static function parseFromFiles($files = array())
    {
        $servers = array();
        foreach ($files as $file) {
            if (is_file($file)) {
                $servers = array_merge($servers, SSHConfig::parse(file_get_contents($file)));
            }
        }
        return $servers;
    }

    // Refering the following code.
    //   https://gist.github.com/geeksunny/3376694
    //   https://github.com/fitztrev/shuttle
    
    /**
     * parse ssh config
     * @param  [type] $contents [description]
     * @return [type]           [description]
     */
    public static function parse($contents)
    {
        $servers = array();
        $lines = explode("\n", $contents);
        $key = null;
        foreach ($lines as $line) {
            $line = trim($line);

            if (preg_match(
                "/^(#?)[\s\t]*([^#\s\t=]+)[\s\t=]+(.*)$/", 
                $line,
                $matches)) {

                if ($matches && count($matches) != 4) {
                    continue;
                }

                $isComment = ($matches[1] == "#");
                $first = $matches[2];
                $second = $matches[3];

//                print_r($matches)."\n";

                if ($isComment)  {
                    continue;
                }

                if ($first == "Host") {
                    // a new host section
                    $key = $second;
                    $servers[$key] = array();
                }

                $servers[$key][strtolower($first)] = $second;
            }
        }

        return $servers;
    }
}