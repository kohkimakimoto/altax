<?php
namespace Kohkimakimoto\Altax\Util;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

use Symfony\Component\Finder\Finder;

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

    public function loadTasks($hosts)
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
            $beforeCount = count(get_declared_classes());
            require_once $file;
            $afterClasses = get_declared_classes();
            $afterCount = count($afterClasses);
            if ($beforeCount < $afterCount) {
                $class = end(get_declared_classes());
                $instance = new $class();
                if (get_parent_class($instance) != "Kohkimakimoto\Altax\Task\BaseTask") {
                    throw new \RuntimeException("Task class must exntends \Kohkimakimoto\Altax\Task\BaseTask.");
                }
                $instance->register();
            }

        }
    }

}