<?php
namespace Altax\Command\Builtin;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Altax\Module\Env\Facade\Env;

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
        $table = $this->getHelperSet()->get('table');
        $table->setHeaders(array('key', 'value'));
        
        $vars = Env::getVars();
        
        foreach ($vars as $key => $value) {
            $table->addRow(array(
                $key,
                $value
            ));
        }

        $table->render($output);
    }
}
