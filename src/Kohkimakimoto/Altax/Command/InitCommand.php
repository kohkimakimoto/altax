<?php
namespace Kohkimakimoto\Altax\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

class InitCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('init')
            ->setDescription('Create default configuration directory in the current directory')
            ->setHelp('
Create default configuration directory in the current directory
            ')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $this->getApplication()->getConfigPath();
        if (is_file($path)) {
            throw new \RuntimeException("$path already exists.");
        }

        $fs = new Filesystem();
        $fs->mkdir(dirname($path), 0755);
        $content = <<< EOL
<?php
/**
 * Altax Configurations.
 *
 * You need to modify this file for your environment.
 *
 * @see https://github.com/kohkimakimoto/altax
 * @author yourname <youremail@yourcompany.com>
 */

EOL;
        file_put_contents($path, $content);
        $output->writeln("<info>Initialized $path</info>");
    }
}
