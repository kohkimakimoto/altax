<?php

namespace Altax\Command\Builtin;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Task;

/**
 * Tasks Command
 *
 * @author Damien Pitard <damien.pitard@gmail.com>
 */
class TasksCommand extends SymfonyCommand
{
    protected function configure()
    {
        $this
            ->setName('tasks')
            ->setDescription('Lists defined tasks')
            ->addOption(
                'format',
                null,
                InputOption::VALUE_REQUIRED,
                'To output list in other formats (txt|txt-no-header|json|json-pretty)',
                'txt'
            )
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tasks = Task::getTasks();

        $format = $input->getOption('format');
        if ('txt' === $format || 'txt-no-header' === $format) {
            if ($tasks) {
                $table = new Table($output);
                $style = new TableStyle();
                $style->setHorizontalBorderChar('')
                    ->setVerticalBorderChar('')
                    ->setCrossingChar('')
                    ->setCellRowContentFormat("%s    ")
                    ;
                $table->setStyle($style);
                if ('txt-no-header' !== $format) {
                    $table->setHeaders(array('name', 'description', 'hidden'));
                }
                foreach ($tasks as $task) {
                    $command = $task->createCommandInstance();
                    $table->addRow(array(
                        $task->getName(),
                        $command->getDescription(),
                        $task->isHidden() ? 'X' : '',
                    ));
                }

                $table->render($output);
            } else {
                $output->writeln('No tasks defined.');
            }
        } elseif ('json' === $format || 'json-pretty' === $format) {
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

            $json = null;
            if ('json-pretty' === $format) {
                $json = json_encode($data, JSON_PRETTY_PRINT);
            } else {
                $json = json_encode($data);
            }

            $output->writeln($json);
        } else {
            throw new \InvalidArgumentException(sprintf('Unsupported format "%s".', $format));
        }
    }
}
