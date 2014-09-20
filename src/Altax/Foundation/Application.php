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
        return static::VERSION." - ".static::COMMIT;
    }

    public function registerBuiltinAliases()
    {
        $aliases = array(
            'App'              => 'Altax\Facade\App',
            'Server'           => 'Altax\Facade\Server',
            'Env'              => 'Altax\Facade\Env',
            'Task'             => 'Altax\Facade\Task',
            'Input'            => 'Altax\Facade\Input',
            'Output'           => 'Altax\Facade\Output',
            'KeyPassphraseMap' => 'Altax\Facade\KeyPassphraseMap',
            'Process'          => 'Altax\Facade\Process',
        );
        AliasLoader::getInstance($aliases)->register();
    }

    public function registerBuiltinProviders()
    {
        $providers = array(
            'Illuminate\Events\EventServiceProvider',
            'Illuminate\Filesystem\FilesystemServiceProvider',
            'Altax\Server\ServerServiceProvider',
            'Altax\Env\EnvServiceProvider',
            'Altax\Task\TaskServiceProvider',
            'Altax\Process\ProcessServiceProvider',
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
