<?php

use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Input\ArgvInput;
use Illuminate\Config\FileLoader;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ClassLoader;

use Altax\Foundation\AliasLoader;
use Altax\Foundation\Application;

/**
 * Boot altax application.
 * @param  array   $configs [description]
 * @param  boolean $cli     [description]
 * @return [type]           [description]
 */
function bootAltaxApplication(array $configs = array(), $cli = true)
{
    if ($cli) {

        if (PHP_SAPI !== 'cli') {
            echo 'Warning: Altax should be invoked via the CLI version of PHP, not the '.PHP_SAPI.' SAPI'.PHP_EOL;
        }

        error_reporting(-1);

        // This code refers to Composer.
        if (function_exists('ini_set')) {
            @ini_set('display_errors', 1);

            $memoryInBytes = function ($value) {
                $unit = strtolower(substr($value, -1, 1));
                $value = (int) $value;
                switch ($unit) {
                    case 'g':
                        $value *= 1024;
                        // no break (cumulative multiplier)
                    case 'm':
                        $value *= 1024;
                        // no break (cumulative multiplier)
                    case 'k':
                        $value *= 1024;
                }

                return $value;
            };

            $memoryLimit = trim(ini_get('memory_limit'));
            // Increase memory_limit if it is lower than 512M
            if ($memoryLimit != -1 && $memoryInBytes($memoryLimit) < 512 * 1024 * 1024) {
                @ini_set('memory_limit', '512M');
            }
            unset($memoryInBytes, $memoryLimit);
        }

    }

    $app = new Application();
    $app->instance('app', $app);

    Facade::clearResolvedInstances();
    Facade::setFacadeApplication($app);

    // Default input and output.
    // Generally, these objects will be overrided by console application process.
    $app->instance('input', new ArgvInput());
    $app->instance('output', new ConsoleOutput());

    $app->instance('config_files', $configs);

    $app->registerBuiltinAliases();
    $app->registerBuiltinProviders();

    return $app;
}

// ------------------------------------------------------------
// helper functions
// ------------------------------------------------------------

/**
 * If it is vector array.
 * @param  [type]  $array [description]
 * @return boolean        [description]
 */
function is_vector($array)
{
    if (array_values($array) === $array) {
      return true;
    } else {
      return false;
    }
}
