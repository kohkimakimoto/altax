<?php
namespace Altax\Foundation;

use Illuminate\Container\Container;

/**
 * Altax Application
 *
 * @author Kohki Makimoto <kohki.makimoto@gmail.com>
 */
class Application extends Container
{
    /**
     * Name of the application.
     */
    const NAME = "Altax";

    /**
     * Version of the application.
     */
    const VERSION = "4.0.0";

    /**
     * git commit hash.
     */
    const COMMIT = "%commit%";

    /**
     * Get a application name
     * @return string name
     */
    public function getName()
    {
        return static::NAME;
    }

    /**
     * Get a version with commit
     * @return string a version with commit
     */
    public function getVersionWithCommit()
    {
        return static::VERSION." (build: ".static::COMMIT.")";
    }

    public function registerProviders($providers)
    {
        foreach ($providers as $provider) {
            with(new $provider($this))->register();
        }
    }
}
