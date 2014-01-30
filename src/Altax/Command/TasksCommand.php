<?php
namespace Altax\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class TasksCommand extends \Altax\Command\Command
{
    protected function configure()
    {
        $this
            ->setName('tasks')
            ->setDescription('Displays registerd tasks')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $tasks = $container->get("tasks");

        $table = $this->getHelperSet()->get('table');
        $table->setHeaders(array('name', 'description'));
        foreach ($tasks as $task) {
            $table->addRow(array(
                $task->name,
                $task->description,
                ));
        }

        $table->render($output);
    }

}