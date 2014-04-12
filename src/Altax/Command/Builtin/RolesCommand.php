<?php
namespace Altax\Command\Builtin;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputDefinition;

/**
 * Roles Command
 */
class RolesCommand extends \Symfony\Component\Console\Command\Command
{
    protected function configure()
    {
        $this
            ->setName('roles')
            ->setDefinition(new InputDefinition(array(
                new InputOption('format', null, InputOption::VALUE_REQUIRED, 'To output list in other formats', 'txt')
            )))
            ->setDescription('Displays roles');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getApplication()->getContainer();
        $roles = $container->get('roles');

        $format = $input->getOption('format');
        if ('txt' === $format) {
            if ($roles) {
                $table = $this->getHelperSet()->get('table');
                $table->setHeaders(array('name', 'nodes'));
                foreach ($roles as $key => $nodes) {
                    $table->addRow(array(
                        $key,
                        trim(implode(', ', $nodes)),
                    ));
                }
                $table->render($output);
            } else {
                $output->writeln('There are not any roles.');
            }
        } else if ('json' === $format) {
            $data = array();
            if ($roles) {
                foreach ($roles as $key => $nodes) {
                    $data[$key] = array(
                        'nodes' => $nodes
                    );
                }
            }
            $output->writeln(json_encode($data));
        } else {
            throw new \InvalidArgumentException(sprintf('Unsupported format "%s".', $format));
        }
    }
}
