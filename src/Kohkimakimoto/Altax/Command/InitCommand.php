<?php
namespace Kohkimakimoto\Altax\Command;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

use Kohkimakimoto\Altax\Command\BaseCommand;

class InitCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('init')
            ->setDescription('Create default configuration directory in the current directory')
            ->addOption(
                'path',
                null,
                InputOption::VALUE_REQUIRED,
                'Creating configuration file path'
                )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configurationPath = $input->getOption("path");
        if (!$configurationPath) {
            $configurationPath = getcwd()."/.altax/config.php";
        }
        
        if (is_file($configurationPath)) {
            throw new \RuntimeException("$configurationPath already exists.");
        }

        $fs = new Filesystem();
        $fs->mkdir(dirname($configurationPath), 0755);
        $content = <<<EOL
<?php
/**
 * Altax Configurations.
 *
 * You need to modify this file for your environment.
 *
 * @see https://github.com/kohkimakimoto/altax
 * @author yourname <youremail@yourcompany.com>
 */

// =========================================================
// Configures by separated configuration files.
//   You can use \Kohkimakimoto\Altax\Util\Configuration 
//   utility class to load separated configuration files.
// =========================================================
// \$configuration = new \Kohkimakimoto\Altax\Util\Configuration();
// \$configuration->loadHosts(array(__DIR__."/hosts.php"));
// \$configuration->loadTasks(array(__DIR__."/tasks"));


// =========================================================
// Configures by defining directly
//   You can define hosts and tasks in this file using some
//   helper functions. For instance host(), task() etc...
// =========================================================

//
// Host and role configurations.
//

host('127.0.0.1', array('web', 'localhost'));
// host('192.168.0.1', 'web');
// host('192.168.0.2', 'web');

// or (Specify SSH Configurations) 

// host('127.0.0.1',   array('port' => '22', 'login_name' => 'yourname', 'identity_file' => '/home/yourname/.ssh/id_rsa'), array('web', 'localhost'));
// host('192.168.0.1', array('port' => '22', 'login_name' => 'yourname', 'identity_file' => '/home/yourname/.ssh/id_rsa'), 'web');
// host('192.168.0.2', array('port' => '22', 'login_name' => 'yourname', 'identity_file' => '/home/yourname/.ssh/id_rsa'), 'web');

//
// The Following is sample task definition.
//
desc('This is a sample task.');
task('sample',array('roles' => 'web'), function(\$host, \$args){

  run('echo Hellow World!');

});

EOL;
        file_put_contents($configurationPath, $content);
        $output->writeln("<info>Created file: </info>$configurationPath");

        // hosts configuration file
        $hostsPath = dirname($configurationPath)."/hosts.php";
        $content = <<<EOL
<?php
/**
 * Altax hosts Configurations.
 */

EOL;
        file_put_contents($hostsPath, $content);
        $output->writeln("<info>Created file: </info>$hostsPath");

        // tasks configuration directory
        $tasksDirPath = dirname($configurationPath)."/tasks";
        $fs->mkdir($tasksDirPath, 0755);
        $output->writeln("<info>Created dir:  </info>$tasksDirPath");
        
    }
}
