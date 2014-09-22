<?php
namespace Altax\Process;

use Symfony\Component\Process\Process as SymfonyProcess;
use Symfony\Component\Filesystem\Filesystem;

class Process
{
    public function __construct($output, $executor, $node)
    {
        $this->output = $output;
        $this->executor = $executor;
        $this->node = $node;
    }

    public function run($commandline, $options = array())
    {
        if (!$this->node) {
            throw new \RuntimeException("Node is not defined to run the command.");
        }

        if (is_array($commandline)) {
            $commandline = implode(" && ", $commandline);
        }

        $this->output->writeln($this->getRemoteInfoPrefix()."<info>Run: </info>$commandline");

        $realCommand = $this->compileRealCommand($commandline, $options);

        if ($this->output->isDebug()) {
            $this->output->writeln($this->getRemoteInfoPrefix()."<info>Real command: </info>$realCommand");
        }

        $ssh = $this->getSSH();

        if (isset($options["timeout"])) {
            $ssh->setTimeout($options["timeout"]);
        } else {
            $ssh->setTimeout(null);
        }

        $output = $this->output;
        $resultContent = null;
        $ssh->exec($realCommand, function ($buffer) use ($output, &$resultContent) {
            $output->write($buffer);
            $resultContent .= $buffer;
        });

        $returnCode = $ssh->getExitStatus();

        return new ProcessResult($returnCode, $resultContent);
    }

    public function runLocally($commandline, $options = array())
    {
        if (is_array($commandline)) {
            $commandline = implode(" && ", $commandline);
        }

        $this->output->writeln($this->getLocalInfoPrefix()."<info>Run: </info>$commandline");

        $realCommand = $this->compileRealCommand($commandline, $options);

        if ($this->output->isDebug()) {
            $this->output->writeln($this->getLocalInfoPrefix()."<info>Real command: </info>$realCommand");
        }

        $symfonyProcess = new SymfonyProcess($realCommand);
        if (isset($options["timeout"])) {
            $symfonyProcess->setTimeout($options["timeout"]);
        } else {
            $symfonyProcess->setTimeout(null);
        }

        $output = $this->output;
        $resultContent = null;
        $returnCode = $symfonyProcess->run(function ($type, $buffer) use ($output, &$resultContent) {
            $output->write($buffer);
            $resultContent .= $buffer;
        });

        return new ProcessResult($returnCode, $resultContent);
    }

    protected function compileRealCommand($commandline, $options)
    {

        $realCommand = "";

        if (isset($options["user"])) {
            $realCommand .= 'sudo -u'.$options["user"].' TERM=dumb ';
        }

        $realCommand .= '/bin/bash -l -c "';

        if (isset($options["cwd"])) {
            $realCommand .= 'cd '.$options["cwd"].' && ';
        }

        $realCommand .= str_replace('"', '\"', $commandline);
        $realCommand .= '"';

        return $realCommand;
    }

    protected function getSSH()
    {
        $ssh = new \Net_SSH2(
            $this->node->getHostOrDefault(),
            $this->node->getPortOrDefault());

        // set up key
        $key = new \Crypt_RSA();

        if ($this->node->useAgent()) {
            // use ssh-agent
            if (class_exists('System_SSH_Agent', true) == false) {
                require_once 'System/SSH_Agent.php';
            }
            $key = new \System_SSH_Agent();
        } else {
            // use ssh key file
            if ($this->node->isUsedWithPassphrase()) {
                // use passphrase
                $key->setPassword($this->node->getPassphrase());
            }

            if (!$key->loadKey($this->node->getKeyContents())) {
                throw new \RuntimeException('Unable to load SSH key file: '.$this->node->getKeyOrDefault());
            }
        }

        // login
        if (!$ssh->login($this->node->getUsernameOrDefault(), $key)) {
            $err = error_get_last();
            $emessage = isset($err['message']) ? $err['message'] : "";
            throw new \RuntimeException('Unable to login '.$this->node->getName().". ".$emessage);
        }

        return $ssh;
    }

    public function get($remote, $local)
    {
        if (!$this->node) {
            throw new \RuntimeException("Node is not defined to get a file.");
        }

        $this->output->writeln($this->getRemoteInfoPrefix()."<info>Get: </info>$remote -> $local");

        $sftp = $this->getSFTP();

        if (!is_dir(dirname($local))) {
            $fs = new Filesystem();
            $fs->mkdir(dirname($local));
            if ($this->output->isDebug()) {
                $this->output->writeln($this->getRemoteInfoPrefix()."<info>Create directory: </info>".dirname($local));
            }
        }

        $ret = $sftp->get($remote, $local);
        if ($ret === false) {
            throw new \RuntimeException("Couldn't get: $remote -> $local");
        }

        return $ret;
    }

    public function getString($remote)
    {
        if (!$this->node) {
            throw new \RuntimeException("Node is not defined to get a file.");
        }

        $this->output->writeln($this->getRemoteInfoPrefix()."<info>Get: </info>$remote");

        $sftp = $this->getSFTP();
        $ret = $sftp->get($remote);
        if ($ret === false) {
            throw new \RuntimeException("Couldn't get: $remote");
        }

        return $ret;
    }

    public function put($local, $remote)
    {
        if (!$this->node) {
            throw new \RuntimeException("Node is not defined to put a file.");
        }

        $this->output->writeln($this->getRemoteInfoPrefix()."<info>Put: </info>$local -> $remote");

        $sftp = $this->getSFTP();

        if (!is_file($local)) {
           throw new \RuntimeException("Couldn't put: $local -> $remote");
        }

        $ret = $sftp->put($remote, $local, NET_SFTP_LOCAL_FILE);
        if ($ret === false) {
            throw new \RuntimeException("Couldn't put: $local -> $remote");
        }

        return $ret;
    }

    public function putString($remote, $contents)
    {
        if (!$this->node) {
            throw new \RuntimeException("Node is not defined to put a file.");
        }

        $this->output->writeln($this->getRemoteInfoPrefix()."<info>Put: </info>$remote");

        $sftp = $this->getSFTP();
        $ret = $sftp->put($remote, $contents);
        if ($ret === false) {
            throw new \RuntimeException("Couldn't put: $remote");
        }

        return $ret;
    }

    protected function getSFTP()
    {
        $sftp = new \Net_SFTP(
            $this->node->getHostOrDefault(),
            $this->node->getPortOrDefault());
        $key = new \Crypt_RSA();
        $key->loadKey($this->node->getKeyContents());
        if (!$sftp->login($this->node->getUsernameOrDefault(), $key)) {
            throw new \RuntimeException('Unable to login '.$this->node->getName());
        }

        return $sftp;
    }

    public function getNodeName()
    {
        $name = null;
        if (!$this->node) {
            $name = "localhost";
        } else {
            $name = $this->node->getName();
        }

        return $name;
    }

    public function getNode()
    {
        return $this->node;
    }
    public function getRemoteInfoPrefix()
    {
        return "<info>[</info><comment>".$this->getNodeName().":".$this->getPid()."</comment><info>]</info> ";
    }

    public function getLocalInfoPrefix()
    {
        return "<info>[</info><comment>localhost:".$this->getPid()."</comment><info>]</info> ";
    }

    protected function getPid()
    {
        $pid = null;
        if (!function_exists('posix_getpid')) {
            $pid = getmypid();
        } else {
            $pid = posix_getpid();
        }

        return $pid;
    }

}
