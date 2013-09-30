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
            require_once $file;
        }
    }

}