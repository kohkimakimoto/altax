<?php

namespace Altax\Command\Builtin;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DescriptorHelper;
use Altax\Module\Env\Facade\Env;

/**
 * Tasks Command
 *
 * @author Damien Pitard <damien.pitard@gmail.com>
 */
class TasksCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('tasks')
            ->setDefinition(new InputDefinition(array(
                new InputOption('format', null, InputOption::VALUE_REQUIRED, 'To output list in other formats', 'txt')
            )))
            ->setDescription('Lists defined tasks');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getApplication()->getContainer();
        $tasks = $container->get('tasks');

        $format = $input->getOption('format');
        if ('txt' === $format) {
            if ($tasks) {
                $table = $this->getHelperSet()->get('table');
                $table->setHeaders(array('name', 'description', 'hidden'));
                foreach ($tasks as $task) {
                    $command = $task->createCommandInstance();
                    $table->addRow(array(
                        $task->getName(),
                        $command->getDescription(),
                        $task->isHidden()?'X':'',
                    ));
                }

                $table->render($output);
            } else {
                $output->writeln('No tasks defined.');
            }
        } else if ('json' === $format) {
            $data = array();
            if ($tasks) {
                foreach ($tasks as $task) {
                    $command = $task->createCommandInstance();
                    $data[$task->getName()] = array(
                        'description' => $command->getDescription(),
                        'hidden' => $task->isHidden()
                    );
                }
            }
            $output->writeln(json_encode($data));
        } else {
            throw new \InvalidArgumentException(sprintf('Unsupported format "%s".', $format));
        }
    }
}
