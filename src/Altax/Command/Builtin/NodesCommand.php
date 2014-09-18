<?php
namespace Altax\Command\Builtin;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Server;

/**
 * Nodes Command
 */
class NodesCommand extends SymfonyCommand
{
    protected function configure()
    {
        $this
            ->setName('nodes')
            ->setDescription('Displays nodes')
            ->addOption(
                'format',
                null,
                InputOption::VALUE_REQUIRED,
                'To output list in other formats (txt|txt-no-header|json)',
                'txt'
            )
            ->addOption(
                'detail',
                'd',
                InputOption::VALUE_NONE,
                'Shows detail infomation'
            )
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $nodes = Server::getNodes();

        $format = $input->getOption('format');
        if ('txt' === $format || 'txt-no-header' === $format) {
            $table = new Table($output);
            $style = new TableStyle();
            $style->setHorizontalBorderChar('')
                ->setVerticalBorderChar('')
                ->setCrossingChar('')
                ->setCellRowContentFormat("%s    ")
                ;
            $table->setStyle($style);

            if ($nodes) {
                $isDetail = $input->getOption('detail');
                if ('txt-no-header' !== $format) {
                    if ($isDetail) {
                        $table->setHeaders(array('name', 'host', 'port', 'username', 'key', 'roles'));
                    } else {
                        $table->setHeaders(array('name', 'roles'));
                    }
                }

                foreach ($nodes as $node) {
                    if ($isDetail) {
                        $table->addRow(array(
                            $node->getName(),
                            $node->getHost(),
                            $node->getPort(),
                            $node->getUsername(),
                            $node->getKey(),
                            trim(implode(',', $node->roles())),
                        ));
                    } else {
                        $table->addRow(array(
                            $node->getName(),
                            trim(implode(',', $node->roles())),
                        ));
                    }
                }
                $table->render($output);
            } else {
                $output->writeln('There are not any nodes.');
            }
        } elseif ('json' === $format) {
            $data = array();
            if ($nodes) {
                $isDetail = $input->getOption('detail');
                foreach ($nodes as $node) {
                    $roleNames = array();
                    foreach ($node->roles() as $roleName => $role) {
                        $roleNames[] = $roleName;
                    }

                    if ($isDetail) {
                        $data[$node->getName()] = array(
                            'host' => $node->getHost(),
                            'port' => $node->getPort(),
                            'username' => $node->getUsername(),
                            'key' => $node->getKey(),
                            'roles' => $roleNames
                        );
                    } else {
                        $data[$node->getName()] = array(
                            'roles' => $roleNames
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
