<?php
namespace Altax\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class NodesCommand extends \Altax\Command\Command
{
    protected function configure()
    {
        $this
            ->setName('nodes')
            ->setDescription('Displays nodes')
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
        $container = $this->getContainer();
        $nodes = $container->get("nodes");

        $isDetail = $input->getOption("detail");

        $table = $this->getHelperSet()->get('table');
        
        if ($isDetail) {
            $table->setHeaders(array('name', 'host', 'port', 'username', 'key', ));
        } else {
            $table->setHeaders(array('name'));
        }

        foreach ($nodes as $node) {
            if ($isDetail) {
                $table->addRow(array(
                    $node->name,
                    $node->host,
                    $node->port,
                    $node->username,
                    $node->key,
                ));
            } else {
                $table->addRow(array(
                    $node->name
                ));
            }
        }

        $table->render($output);
    }

}