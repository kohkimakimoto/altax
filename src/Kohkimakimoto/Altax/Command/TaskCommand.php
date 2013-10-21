<?php
namespace Kohkimakimoto\Altax\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

use Kohkimakimoto\Altax\Command\BaseCommand;
use Kohkimakimoto\Altax\Util\Context;
use Kohkimakimoto\Altax\Task\Executor;

class TaskCommand extends BaseCommand
{
    protected $desc = null;
    protected $callback = null;
    protected $taskOptions = array();

    public function configureByTask($task)
    {
        if (isset($task['desc'])) {
          $this->desc = $task['desc'];
        }

        if (isset($task['callback'])) {
          $this->callback = $task['callback'];
        }

        if (isset($task['options'])) {
          $this->taskOptions = $task['options'];
        }

        $this
            ->setDescription($this->desc)
            ->addArgument(
                'args',
                InputArgument::IS_ARRAY,
                'Arguments passed to the task.'
            )
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $application = $this->getApplication();
        
        $applicatonName = $application->getName();
        $applicatonVersion = $application->getVersion();
        $name = $this->getName();
    
        if (isset($this->taskOptions['quiet']) 
            && $this->taskOptions['quiet'] 
            && !$input->getOption('debug')) {
            $output->setVerbosity(OutputInterface::VERBOSITY_QUIET);
        }

        $output->writeln("- Starting process of <info>$applicatonName</info> version <comment>$applicatonVersion </comment>");

        $executor = new Executor();
        $executor->execute($name, $input, $output);
    }
}
