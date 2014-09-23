<?php
namespace Altax\Command\Builtin;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Env;

/**
 * Env Command
 */
class EnvCommand extends SymfonyCommand
{
    protected function configure()
    {
        $this
            ->setName('env')
            ->setDescription('Displays environment')
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
        $parameters = Env::parameters();

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

            if ('txt-no-header' !== $format) {
                $table->setHeaders(array('key', 'value'));
            }

            foreach ($parameters as $key => $value) {
                $table->addRow(array(
                    $key,
                    $value
                ));
            }
            $table->render($output);
        } elseif ('json' === $format || 'json-pretty' === $format) {
            $json = null;
            if ('json-pretty' === $format) {
                $json = json_encode($parameters, JSON_PRETTY_PRINT);
            } else {
                $json = json_encode($parameters);
            }
            $output->writeln($json);
        } else {
            throw new \InvalidArgumentException(sprintf('Unsupported format "%s".', $format));
        }
    }
}
