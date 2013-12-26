<?php
namespace Kohkimakimoto\Altax\Util;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

use Symfony\Component\Finder\Finder;

/**
 * @deprecated
 * @codeCoverageIgnore
 */
class Configuration
{
    public function __construct()
    {
        $this->configure();
    }

    protected function configure()
    {
    }

    public function loadHosts($hosts)
    {
        if (is_string($hosts)) {
            $hosts = array($hosts);
        }

        $files = array();
        foreach ($hosts as $host) {
            if (is_dir($host)) {
                $finder = new Finder();
                $finder->files()->in($host)->name('*.php');
                foreach ($finder as $file) {
                    $files[] = $file->getRealpath();
                }
            }

            if (is_file($host)) {
                $files[] = realpath($host);
            }
        }

        foreach ($files as $file) {
            $hostsArray = require_once $file;
            if (!is_array($hostsArray)) {
                continue;
            }
            foreach ($hostsArray as $host => $options) {
                host($host, $options);
            }
        }
    }

    public function registerTasks($tasks)
    {
        if (!is_array($tasks)) {
            $tasks = array($tasks);
        }
        foreach ($tasks as $task) {
            $task->register();
        }
    }

    public function loadTasks($tasks)
    {
        if (is_string($tasks)) {
            $tasks = array($tasks);
        }

        $files = array();
        foreach ($tasks as $task) {
            if (is_dir($task)) {
                $finder = new Finder();
                $finder->files()->in($task)->name('*.php');
                foreach ($finder as $file) {
                    $files[] = $file->getRealpath();
                }
            }

            if (is_file($task)) {
                $files[] = realpath($task);
            }
        }

        foreach ($files as $file) {
            $beforeCount = count(get_declared_classes());
            require_once $file;
            $afterClasses = get_declared_classes();
            $afterCount = count($afterClasses);
            if ($beforeCount < $afterCount) {
                $class = end(get_declared_classes());
                $instance = new $class();
                $instance->register();
            }

        }
    }

}