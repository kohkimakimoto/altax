<?php
namespace Altax\Command\Builtin;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Roles Command
 */
class SelfUpdateCommand extends \Symfony\Component\Console\Command\Command
{
    protected function configure()
    {
        $this
            ->setName('self-update')
            ->setDescription('Updates altax to the latest version.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getApplication()->getContainer();

        $commandFile = realpath($_SERVER['SCRIPT_FILENAME']);

        $currentVersion = "v".$container->getVersion();
        $updateVersion = trim(@file_get_contents('https://raw.githubusercontent.com/kohkimakimoto/altax/master/version'));

        if (!$container->isPhar()) {
            $output->writeln('<error>You can not update altax. Because altax only supports self-update command on PHAR file version.</error>');
            return 1;
        }

        if (!preg_match('/^v[0-9].[0-9]+.[0-9]+$/', $updateVersion)) {
            $output->writeln('<error>You can not update altax. Because the latest version of altax are not available for download.</error>');
            return 1;
        }

        if ($currentVersion === $updateVersion) {
            $output->writeln('<info>You are already using altax version <comment>'.$updateVersion.'</comment>.</info>');
            return 0;
        }

        $output->writeln(sprintf("Updating to version <info>%s</info>.", $updateVersion));

        $tmpDir = "/tmp/".uniqid("altax.update.");

        $process = new Process("mkdir $tmpDir && cd $tmpDir && curl -L https://raw.githubusercontent.com/kohkimakimoto/altax/master/installer.sh | bash -s local $updateVersion");
        $process->setTimeout(null);
        if ($process->run() !== 0) {
            $output->writeln('<error>You can not update altax.');
            return 1;
        }

        $fs = new Filesystem();
        $fs->copy($tmpDir."/altax.phar", $commandFile, true);
        $fs->remove($tmpDir);
        $output->writeln("Done.");
    }
}
