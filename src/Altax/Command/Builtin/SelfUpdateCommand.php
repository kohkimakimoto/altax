<?php
namespace Altax\Command\Builtin;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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

        $currentVersion = "v".$container->getVersion();
        $updateVersion = trim(@file_get_contents('https://raw.github.com/kohkimakimoto/altax/self-update/version'));


        if (!preg_match('{^v[0-9].[0-9].[0-9]$}', $updateVersion)) {
            $output->writeln('<error>You can not update altax. Because the latest version of altax are not available for download.</error>');
            return 1;
        }

        if ($currentVersion === $updateVersion) {
            $output->writeln('<info>You are already using altax version <comment>'.$updateVersion.'</comment>.</info>');
            return 0;
        }

        $output->writeln(sprintf("Updating to version <info>%s</info>.", $updateVersion));
    }

}