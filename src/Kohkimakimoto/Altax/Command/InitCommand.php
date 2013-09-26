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
            $configurationPath = getcwd()."/.altax/altax.php";
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

//
// Host and role configurations.
//
role('web', '127.0.0.1');

// or

// role('web', array('192.168.0.1', '192.168.0.2'));

// or

// host('192.168.0.1', 'web');
// host('192.168.0.2', 'web');

// or (Specify SSH Configurations) 

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
        $output->writeln("<info>Initialized $configurationPath</info>");
    }
}
