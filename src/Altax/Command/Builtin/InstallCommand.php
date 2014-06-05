<?php
namespace Altax\Command\Builtin;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Composer\Console\Application as ComposerApplication;
use Composer\IO\ConsoleIO;
use Composer\Factory;

class InstallCommand extends \Composer\Command\InstallCommand
{
    protected function configure()
    {
        parent::configure();
        $this
            ->setDescription("Installs plugin packages from the .altax/composer.json under the current directory.")
            ->addOption(
                '--working-dir',
                '-d',
                InputOption::VALUE_REQUIRED,
                'If specified, use the given directory as working directory.'
                )
            ->addOption(
                '--global',
                '-g',
                InputOption::VALUE_NONE,
                "If specified, use user home configuration '~/.altax/composer.json'"
                )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $newWorkDir = $this->getNewWorkingDir($input);
        if (!is_dir($newWorkDir)) {
            throw new \RuntimeException("Not found directory:".$newWorkDir);
        }

        $oldWorkingDir = getcwd();
        chdir($newWorkDir);
        
        $io = new ConsoleIO($input, $output, $this->getHelperSet());
        $composer = Factory::create($io);
        $this->setComposer($composer);
        $this->setIO($io);

        $statusCode = parent::execute($input, $output);
        
        if (isset($oldWorkingDir)) {
            chdir($oldWorkingDir);
        }

        return $statusCode;
    }

    /**
     * @param  InputInterface    $input
     * @throws \RuntimeException
     */
    private function getNewWorkingDir(InputInterface $input)
    {
        if($input->getOption('global')) {
            return  getenv("HOME")."/.altax";
        }

        $workingDir = $input->getParameterOption(array('--working-dir', '-d'));
        if (false !== $workingDir && !is_dir($workingDir)) {
            throw new \RuntimeException('Invalid working directory specified.');
        }

        if (false === $workingDir) {
            $workingDir = getcwd()."/.altax";
        }

        if (!is_dir($workingDir)) {
            throw new \RuntimeException('Invalid working directory.');
        }

        return $workingDir;
    }
}
