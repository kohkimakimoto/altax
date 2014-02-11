<?php
namespace Altax\Command\Builtin;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Init Command
 */
class InitCommand extends \Symfony\Component\Console\Command\Command
{
    const TEMPLATE = <<<EOL
<?php
// Setup autoloading for plugin command classes.
if (is_file(__DIR__ . '/vendor/autoload.php')) require_once __DIR__ . '/vendor/autoload.php';

// ***************************************************************
// Server definition.
// ***************************************************************
//
// Examples: 
//
//   Server::node("web1.example.com", array("web", "production"));
//   Server::node("web2.example.com", array("web", "production"));
//   Server::node("db1.example.com",  array("db", "production"));
//   Server::node("dev1.example.com", "development");
//

// ***************************************************************
// Task definision 
// ***************************************************************
//
// Examples: 
//
//   Task::register('hello', function(\$task){
//   
//       \$task->process('echo hello world!')->runLocally();
// 
//   });
//
//   Task::register('server', 'Altax\\Command\\ServerCommand');
//

EOL;
    
    const COMPOSER_TEMPLATE = <<<EOL
{
  "require": {
    "php": ">=5.3.0",
    "kohkimakimoto/altax-server": "dev-master"
  }
}

EOL;

    protected function configure()
    {
        $this
            ->setName('init')
            ->addOption(
                'path',
                null,
                InputOption::VALUE_REQUIRED,
                'Creating configuration file path'
                )
            ->setDescription('Creates default configuration directory and files under the current directory')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configurationPath = $input->getOption("path");
        if (!$configurationPath) {
            $configurationPath = getcwd()."/.altax/config.php";
        }

        if (!is_file($configurationPath)) {
            $this->generateConfig($configurationPath, $output);
        } else {
            $output->writeln("<error>File '$configurationPath' is already exists. Skiped creation process.</error>");
        }

        $composerFile = dirname($configurationPath)."/composer.json";
        if (!is_file($composerFile)) {
            $this->generateComposerFile($composerFile, $output);
        } else {
            $output->writeln("<error>File '$composerFile' is already exists. Skiped creation process.</error>");
        }
     }

     protected function generateConfig($configurationPath, $output)
     {
        $fs = new Filesystem();
        $fs->mkdir(dirname($configurationPath), 0755);
        file_put_contents($configurationPath, self::TEMPLATE);
        $output->writeln("<info>Created file: </info>$configurationPath");
     }

     protected function generateComposerFile($composerFile, $output)
     {
        file_put_contents($composerFile, self::COMPOSER_TEMPLATE);
        $output->writeln("<info>Created file: </info>$composerFile");
     }
}