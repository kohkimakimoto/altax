<?php

use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Application as SymfonyApplication;
use Illuminate\Support\Facades\Facade;
use Altax\Foundation\Application;
use Altax\Foundation\AliasLoader;

/**
 * Boot altax application.
 * @param  array   $configs [description]
 * @param  boolean $cli     [description]
 * @return [type]           [description]
 */
function bootAltaxApplication(array $bootstraps = array(), $cli = true)
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

    $env = array();
    // Configure default environment of Altax.
    require __DIR__."/bootstrap.php";

    // Configure custom environemnt of Altax.
    foreach ($bootstraps as $bootstrap) {
        if (is_file($bootstrap)) {
            require $bootstrap;
        }
    }

    $app = new Application();
    $app->instance('app', $app);

    Facade::clearResolvedInstances();
    Facade::setFacadeApplication($app);

    // Default input, output and console.
    // Generally, these objects will be overrided by console application process.
    $app->instance('input', new ArgvInput());
    $app->instance('output', new ConsoleOutput());
    $app->instance('console', new SymfonyApplication());

    $app->registerProviders($env['providers']);

    $app['env']->updateFromArray($env);
    $app['process.runtime']->bootMasterProcess();

    AliasLoader::getInstance($env['aliases'])->register();

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
