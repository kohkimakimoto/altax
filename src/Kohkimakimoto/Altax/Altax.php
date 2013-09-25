<?php
namespace Kohkimakimoto\Altax;

use Symfony\Component\Console\Application;
use Kohkimakimoto\Altax\Command\InitCommand; 

/**
 * Altax
 * @author Kohki Makimoto <kohki.makimoto@gmail.com>
 */
class Altax extends Application
{
    const VERSION = '2.0';
    const HELP_MESSAGES  =<<<EOL

<info>%s</info> version <comment>%s</comment>

Altax is a simple deployment tool running SSH in parallel.
Copyright (c) Kohki Makimoto <kohki.makimoto@gmail.com>
Apache License 2.0
EOL;

    protected $baseDir = null;

    public function __construct()
    {
        parent::__construct();

        $this->setName("Altax");
        $this->setVersion(self::VERSION);

        $this->addCommands(array(
          new InitCommand()
        ));

        $this->baseDir = getcwd();
    }

    public function getLongVersion()
    {
        return sprintf(self::HELP_MESSAGES, $this->getName(), $this->getVersion());
    }

    public function getBaseDir()
    {
        return $this->baseDir;
    }

    public function getConfigDir()
    {
        return $this->baseDir."/.altax";
    }

    public function getConfigPath()
    {
        return $this->baseDir."/.altax/altax.php";
    }

}


