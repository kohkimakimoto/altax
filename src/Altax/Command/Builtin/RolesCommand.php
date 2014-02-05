<?php
namespace Altax\Command\Builtin;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class RolesCommand extends \Symfony\Component\Console\Command\Command
{
    protected function configure()
    {
        $this
            ->setName('roles')
            ->setDescription('Displays roles')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getApplication()->getContainer();
        $roles = $container->get("roles");

        $table = $this->getHelperSet()->get('table');

        $table->setHeaders(array('name', 'nodes'));

        foreach ($roles as $key => $nodes) {
            $table->addRow(array(
                $key,
                implode(", ", $nodes),
            ));
        }

        $table->render($output);
    }

}