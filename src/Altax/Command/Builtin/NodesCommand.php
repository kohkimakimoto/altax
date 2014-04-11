<?php
namespace Altax\Command\Builtin;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputDefinition;

/**
 * Nodes Command
 */
class NodesCommand extends \Symfony\Component\Console\Command\Command
{
    protected function configure()
    {
        $this
            ->setName('nodes')
            ->setDefinition(new InputDefinition(array(
                new InputOption('format', null, InputOption::VALUE_REQUIRED, 'To output list in other formats', 'txt')
            )))
            ->setDescription('Displays nodes')
            ->addOption(
                'detail',
                'd',
                InputOption::VALUE_NONE,
                'Shows detail infomation'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getApplication()->getContainer();
        $nodes = $container->get('nodes');

        $format = $input->getOption('format');
        if ('txt' === $format) {
            if ($nodes) {
                $isDetail = $input->getOption('detail');
                $table = $this->getHelperSet()->get('table');

                if ($isDetail) {
                    $table->setHeaders(array('name', 'host', 'port', 'username', 'key', 'roles'));
                } else {
                    $table->setHeaders(array('name', 'roles'));
                }

                foreach ($nodes as $node) {
                    if ($isDetail) {
                        $table->addRow(array(
                            $node->getName(),
                            $node->getHost(),
                            $node->getPort(),
                            $node->getUsername(),
                            $node->getKey(),
                            trim(implode(', ', $node->getReferenceRoles())),
                        ));
                    } else {
                        $table->addRow(array(
                            $node->getName(),
                            trim(implode(', ', $node->getReferenceRoles())),
                        ));
                    }
                }

                $table->render($output);
            } else {
                $output->writeln('There are not any nodes.');
            }
        } else if ('json' === $format) {
            $data = array();
            if ($nodes) {
                $isDetail = $input->getOption('detail');
                foreach ($nodes as $node) {
                    if ($isDetail) {
                        $data[$node->getName()] = array(
                            'host' => $node->getHost(),
                            'port' => $node->getPort(),
                            'username' => $node->getUsername(),
                            'key' => $node->getKey(),
                            'roles' => $node->getReferenceRoles()
                        );
                    } else {
                        $data[$node->getName()] = array(
                            'roles' => $node->getReferenceRoles()
                        );
                    }
                }
            }
            $output->writeln(json_encode($data));
        } else {
            throw new \InvalidArgumentException(sprintf('Unsupported format "%s".', $format));
        }
    }
}
