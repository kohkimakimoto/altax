<?php

namespace Altax\Console;

use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\ListCommand;
use Symfony\Component\Finder\Finder;

use Altax\Foundation\Application as BaseApplication;

/**
 * Altax console application
 */
class Application extends SymfonyApplication
{
    const HELP_MESSAGES =<<<EOL
<info>%s</info> version <comment>%s</comment>

Altax is a deployment tool for PHP.

Copyright (c) Kohki Makimoto <kohki.makimoto@gmail.com>
Apache License 2.0
EOL;

    /**
     * Application container instance.
     */
    protected $container;

    public function __construct(BaseApplication $container)
    {
        parent::__construct(
            $container->getName(),
            $container->getVersionWithCommit());

        $this->container = $container;
        $this->container->instance('console', $this);
    }

    public function getLongVersion()
    {
        return sprintf(self::HELP_MESSAGES, $this->getName(), $this->getVersion());
    }

    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        return parent::run($input, $output);
    }

    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->container->instance('input', $input);
        $this->container->instance('output', $output);

        $this->registerBuiltinCommands();
        $this->loadConfiguration();
        $this->registerTasksAsConsoleCommands();

        // Runs specified command under the symfony console.
        return parent::doRun($input, $output);
    }

    protected function registerBuiltinCommands()
    {
        $finder = new Finder();
        $finder->files()->name('*Command.php')->in(__DIR__."/../Command/Builtin");
        foreach ($finder as $file) {
            $class = "Altax\Command\Builtin\\".$file->getBasename('.php');
            $r = new \ReflectionClass($class);
            $command = $r->newInstance();
            $this->add($command);
        }
    }

    protected function loadConfiguration()
    {
        $input = $this->container["input"];
        $output = $this->container["output"];
        $env = $this->container["env"];

        // Additional configuration file by the cli option.
        if (true === $input->hasParameterOption(array('--file', '-f'))) {
            $configs = $env->get("config.paths");

            $file = $input->getParameterOption(array('--file', '-f'));
            if (!file_exists($file)) {
                throw new \RuntimeException("File not found: $file");
            }
            $configs[] = $file;
            $env->set("config.paths", $configs);
        }

        $command = $this->getCommandName($input);
        if ($command == 'require' || $command == 'install' || $command == 'update') {
            // These are composer task. so don't need to load configuration for altax.
            return;
        }

        $i = 1;
        foreach ($env->get("config.paths") as $file) {
            if ($output->isDebug()) {
                $output->write("Load config $i: $file");
            }
            if ($file && is_file($file)) {
                require $file;
                if ($output->isDebug()) {
                    $output->writeln(" (OK)");
                }
                $i++;
            } else {
                if ($output->isDebug()) {
                    $output->writeln(" (Not found)");
                }
            }
        }
    }

    protected function registerTasksAsConsoleCommands()
    {
        $tasks = $this->container["task"]->getTasks();
        foreach ($tasks as $task) {
            $this->add($task->makeCommand());
        }
    }

    public function getContainer()
    {
        return $this->container;
    }

    protected function getDefaultInputDefinition()
    {
        $definition = parent::getDefaultInputDefinition();
        $definition->addOptions(array(
            new InputOption('--file', '-f', InputOption::VALUE_REQUIRED, 'Specifies configuration file to load.')
        ));

        return $definition;
    }

    public function all($namespace = null)
    {
        $commands = parent::all($namespace);

        // Remove hidden command to prevent listing commands by ListCommand
        foreach ($commands as $name => $command) {
            if (method_exists($command, "getTask")) {
                // Consider the command Altax\Command\Command instance
                $task = $command->getTask();
                if ($task->isHidden()) {
                    unset($commands[$name]);
                }
            }
        }

        return $commands;
    }
}
