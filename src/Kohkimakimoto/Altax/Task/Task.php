<?php
namespace Kohkimakimoto\Altax\Task;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Kohkimakimoto\Altax\Util\Context;

/**
 * Task at a host.
 */
class Task
{
    protected $taskName;
    protected $host;

    public function __construct($taskName, $host, InputInterface $input, OutputInterface $output)
    {
        $this->taskName = $taskName;
        $this->host = $host;
        $this->input = $input;
        $this->output = $output;
    }

    public function execute()
    {
        $input = $this->input;
        $output = $this->output;

        $context = Context::getInstance();

        $callback = $context->get('tasks/'.$this->taskName.'/callback');

        $output->writeln("    - Running <info>".$this->taskName."</info> at <info>".$this->host."</info>");

        $callback($this->host, $input->getArgument('args'));
    }

    public function runSSH($command, $options = array())
    {
        if (!$this->host) {
            throw new \RuntimeException('Host is not specified.');
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
            $realCommand .= "sudo -u".$options['user']." ";
        }

        $realCommand .= "sh -c '";

        if (isset($options['cwd'])) {
            $realCommand .= "cd ".$options['cwd']." && ";
        }

        $realCommand .= $command;

        $realCommand .= "'";

        if ($context->get("debug") === true) {
           $this->output->writeln("      <comment>Debug: </comment>Running command using ssh: $realCommand");
        }

        $ssh->exec($realCommand, function ($str) use ($host) {
            $this->output->write("      <info>$this->host: </info>$str");
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

        $output = null;
        $ret = null;

        $this->output->writeln("      Command: <comment>$command</comment>");

        if ($context->get("debug") === true) {
           $this->output->writeln("      <comment>Debug: </comment>Running local command: $realCommand");
        }

        $descriptorspec = array();

        // Not Use SSH
        $process = proc_open($realCommand, $descriptorspec, $pipes);
        foreach ($pipes as $pipe) {
            fclose($pipe);
        }
        proc_close($process);
    }

    public function getInput()
    {
        return $this->input;
    }

    public function getOutput()
    {
        return $this->output;
    }
}