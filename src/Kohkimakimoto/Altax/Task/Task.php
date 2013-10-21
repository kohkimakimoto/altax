<?php
namespace Kohkimakimoto\Altax\Task;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

use Kohkimakimoto\Altax\Util\Context;

/**
 * Task at a host.
 */
class Task
{
    protected $taskName;
    protected $host;

    public function __construct($taskName, $host, InputInterface $input, OutputInterface $output, $localRun)
    {
        $this->taskName = $taskName;
        $this->host = $host;
        $this->input = $input;
        $this->output = $output;
        $this->localRun = $localRun;

    }

    public function execute()
    {
        $input = $this->input;
        $output = $this->output;

        $context = Context::getInstance();

        $callback = $context->get('tasks/'.$this->taskName.'/callback');
        if (!$callback) {
            throw new \RuntimeException("Callback function for ".$this->taskName." not found.");
        }

        if ($this->localRun) {
            $output->writeln("- Executing task <info>".$this->taskName."</info>");
        } else {
            $output->writeln("- Executing task <info>".$this->taskName."</info> at <info>".$this->host."</info>");
        }

        $callback($this->host, $input->getArgument('args'));
    }

    public function runSSH($command, $options = array())
    {
        if (!$this->host) {
            throw new \RuntimeException('Host is not specified.');
        }

        if ($this->isLocalRun()) {
            return $this->runLocalCommand($command, $options);
        }

        $context = Context::getInstance();

        $sshLoginName = $context->get('hosts/'.$this->host.'/login_name', getenv("USER"));
        $sshIdentityFile = $context->get('hosts/'.$this->host.'/identity_file', getenv("HOME").'/.ssh/id_rsa');
        $port = $context->get('hosts/'.$this->host."/port", 22);
        $host = $context->get('hosts/'.$this->host."/host", $this->host);

        $ssh = new \Net_SSH2($host, $port);
        $key = new \Crypt_RSA();
        $key->loadKey(file_get_contents($sshIdentityFile));
        if (!$ssh->login($sshLoginName, $key)) {
            throw new \RuntimeException('Got a error to login to '.$host);
        }

        $realCommand = "";
       if (isset($options['user'])) {
            $realCommand .= "sudo -u".$options['user']." TERM=dumb ";
        }

        $realCommand .= "sh -c '";

        if (isset($options['cwd'])) {
            $realCommand .= "cd ".$options['cwd']." && ";
        }

        $realCommand .= $command;

        $realCommand .= "'";

        $this->output->writeln("  Command: <comment>$command</comment>");

        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG) {
           $this->output->writeln("  <comment>Debug: </comment>Actual Command: $realCommand");
        }

        $self = $this;
        $ssh->exec($realCommand, function ($str) use ($self) {
            $self->writeCommandOutput($str);
        });
    }

    public function runLocalCommand($command, $options = array())
    {
        $context = Context::getInstance();

        $realCommand = null;

        if (isset($options['user'])) {
            $realCommand .= " sudo -u".$options['user']." ";
        }

        $realCommand .= "sh -c '";

        if (isset($options['cwd'])) {
            $realCommand .= "cd ".$options['cwd']." && ";
        }

        $realCommand .= $command;

        $realCommand .= "'";

        $this->output->writeln("  Command at local: <comment>$command</comment>");

        if ($this->output->getVerbosity() >= OutputInterface::VERBOSITY_DEBUG) {
           $this->output->writeln("  <comment>Debug: </comment>Actual Command: $realCommand");
        }

        $self = $this;
        $process = new Process($realCommand);
        $process->run(function ($type, $buffer) use ($self) {
            $self->writeCommandOutput($buffer);
        });

    }

    public function getInput()
    {
        return $this->input;
    }

    public function getOutput()
    {
        return $this->output;
    }

    public function isLocalRun()
    {
        return $this->localRun;
    }

    protected function writeCommandOutput($str)
    {
        $context = Context::getInstance();

        $taskQuiet = $context->get('tasks/'.$this->taskName.'/options/quiet');
        $inputQuiet = $this->getInput()->getOption('quiet');

        if ($taskQuiet && !$inputQuiet) {
            $this->output->setVerbosity(OutputInterface::VERBOSITY_NORMAL);
            $this->output->write($str);
            $this->output->setVerbosity(OutputInterface::VERBOSITY_QUIET);
        } else {
            $this->output->write($str);
        }
    }
}