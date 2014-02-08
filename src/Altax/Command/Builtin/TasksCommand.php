<?php
namespace Altax\Command\Builtin;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class TasksCommand extends \Symfony\Component\Console\Command\Command
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
        $container = $this->getApplication()->getContainer();
        $tasks = $container->get("tasks");

        if ($tasks) {
            $table = $this->getHelperSet()->get('table');
            $table->setHeaders(array('name', 'description'));
            foreach ($tasks as $task) {
                $table->addRow(array(
                    $task->getName(),
                    $task->getDescription(),
                    ));
            }

            $table->render($output);
        } else {
            $output->writeln("There are not any tasks.");
        }
    }

}