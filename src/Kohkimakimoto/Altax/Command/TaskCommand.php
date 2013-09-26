<?php
namespace Kohkimakimoto\Altax\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

use Kohkimakimoto\Altax\Util\Context;

class TaskCommand extends BaseCommand
{
    protected $desc;
    protected $callback;
    protected $taskOptions;

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

        $this->setDescription($this->desc);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    }
}
