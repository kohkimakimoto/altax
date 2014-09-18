<?php
namespace Altax\Foundation;

use Illuminate\Container\Container;
use Altax\Foundation\AliasLoader;

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
    const VERSION = "3.1.0";

    /**
     * git commit hash.
     */
    const COMMIT = "%commit%";

    public function getName()
    {
        return static::NAME;
    }

    public function getVersionWithCommit()
    {
        return static::VERSION." - ".static::COMMIT;
    }

    public function registerBuiltinAliases()
    {
        $aliases = array(

        );
        AliasLoader::getInstance($aliases)->register();
    }

    public function registerBuiltinProviders()
    {
        $providers = array(

        );
        $this->registerProviders($providers);
    }

    public function registerProviders($providers)
    {
        foreach ($providers as $provider) {
            with(new $provider($this))->register();
        }
    }
}
