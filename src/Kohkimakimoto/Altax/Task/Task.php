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
        $context = Context::getInstance();

        $sshcmd = $this->getSSHCommandBase();
        $sshcmd .= ' "';
        
        if (isset($options['user'])) {
            $sshcmd .= " sudo -u".$options['user']." ";
        }

        $sshcmd .= "sh -c '";

        if (isset($options['cwd'])) {
            $sshcmd .= "cd ".$options['cwd']."; ";
        }

        $sshcmd .= $command;

        $sshcmd .= '\'"';

        $output = null;
        $ret = null;

        if ($context->get("debug") === true) {
           $this->output->writeln("      Running command: $sshcmd");
        }
        //
        // Get Pseudo-terminal used for temporary.
        //
        // In order to execute command that needs termial, SSH command uses -t option to get a pseudo-terminal.
        // But default pseudo-terminal is connectting other process as Altax Task.
        // the STDOUT of Altax Tasks put data into STDIN of other Altax task in parallel process.
        // It' bad to causes of errors.
        //
        // So, following code is to get Pseudo-terminal used for temporary.
        // this Pseudo-terminal is disconnected other terminal of parallel process
        //
        $descriptorspec = array(
            0 =>  array("file", '/dev/ptmx', 'r'),
        );

        $process = proc_open($sshcmd, $descriptorspec, $pipes);
        foreach ($pipes as $pipe) {
            fclose($pipe);
        }

        proc_close($process);
    }

    public function getSSHCommandBase()
    {
        $context = Context::getInstance();

        if (!$this->host) {
            throw new \RuntimeException('Host is not specified.');
        }

        $sshcmd = "ssh -t";

        $sshLoginName = $context->get('hosts/'.$this->host.'/login_name');
        if ($sshLoginName) {
            $sshcmd .= " -l $sshLoginName";
        }

        $sshIdentityFile = $context->get('hosts/'.$this->host.'/identity_file');
        if ($sshIdentityFile) {
            $sshcmd .= " -i $sshIdentityFile";
        }

        $port = $context->get('hosts/'.$this->host."/port");
        if ($port) {
            $sshcmd .= " -p $port";
        }

        $host = $context->get('hosts/'.$this->host."/host", $this->host);

        $sshcmd .= " $host";
        return $sshcmd;
    }

    public function runLocalCommand($command, $options = array())
    {
        $context = Context::getInstance();

        $cmd = null;

        if (isset($options['user'])) {
            $cmd .= " sudo -u".$options['user']." ";
        }

        $cmd .= "sh -c '";

        if (isset($options['cwd'])) {
            $cmd .= "cd ".$options['cwd']."; ";
        }

        $cmd .= $command;

        $cmd .= '\'';

        $output = null;
        $ret = null;

        if ($context->get("debug") === true) {
           $this->output->writeln("      Running local command: $cmd");
        }

        $descriptorspec = array();

        // Not Use SSH
        $process = proc_open($cmd, $descriptorspec, $pipes);
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