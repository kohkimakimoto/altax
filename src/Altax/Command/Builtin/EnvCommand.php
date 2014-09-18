<?php
namespace Altax\Command\Builtin;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
use Env;

/**
 * Env Command
 */
class EnvCommand extends \Symfony\Component\Console\Command\Command
{
    protected function configure()
    {
        $this
            ->setName('env')
            ->setDescription('Displays environment')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $table = new Table($output);
        $style = new TableStyle();
        $style->setHorizontalBorderChar('')
            ->setVerticalBorderChar('')
            ->setCrossingChar('')
            ->setCellRowContentFormat("%s    ")
            ;
        $table->setStyle($style);

        $table->setHeaders(array('key', 'value'));

        $parameters = Env::parameters();
        foreach ($parameters as $key => $value) {
            $table->addRow(array(
                $key,
                $value
            ));
        }

        $table->render($output);
    }
}
