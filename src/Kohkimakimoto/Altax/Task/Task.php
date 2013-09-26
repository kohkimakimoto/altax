<?php
namespace Kohkimakimoto\Altax\Task;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Kohkimakimoto\Altax\Util\Context;

class Task
{
    protected $taskName;
    protected $host;

    public function __construct($taskName, $host)
    {
        $this->taskName = $taskName;
        $this->host = $host;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        // Get callback function;
        $context = Context::getInstance();
        $this->input = $input;
        $this->output = $output;

        $callback = $context->get('tasks/'.$this->taskName.'/callback');

        $output->writeln("");
        $output->writeln("    - Run <info>".$this->taskName."</info> at <info>".$this->host."</info>");

        $callback($this->host, $input->getArgument('args'));

        $output->writeln("");
    }

    public function runSSH($command, $options = array())
    {

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

        $this->output->writeln("      Running command: $sshcmd");

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
}