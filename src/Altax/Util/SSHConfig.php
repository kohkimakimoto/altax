<?php
namespace Altax\Util;

/**
 * Utility class for ssh config.
 */
class SSHConfig
{

    public static function parseToNodeOptionsFormFiles($files = array())
    {
        $nodesOptions = array();
        $servers = self::parseFromFiles($files);
        foreach ($servers as $key => $config) {
            if (strpos($key, "*") !== false) {
                continue;
            }

            $nodesOptions[$key] = array();

            if (isset($config["hostname"])) {
                $nodesOptions[$key]["host"] = $config["hostname"];
            }

            if (isset($config["port"])) {
                $nodesOptions[$key]["port"] = $config["port"];
            }

            if (isset($config["user"])) {
                $nodesOptions[$key]["username"] = $config["user"];
            }

            if (isset($config["identityfile"])) {
                $nodesOptions[$key]["key"] = $config["identityfile"];
            }

        }
        return $nodesOptions;
    }

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