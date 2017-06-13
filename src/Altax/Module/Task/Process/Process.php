<?php
namespace Altax\Module\Task\Process;

use Symfony\Component\Process\Process as SymfonyProcess;
use Symfony\Component\Filesystem\Filesystem;
use Altax\Module\Server\Facade\Server;
use Altax\Module\Server\Resource\Node;
use Altax\Module\Task\Process\ProcessResult;
use Altax\Util\Arr;
use Altax\Util\SSHKey;

class Process
{
    protected $node;
    protected $runtimeTask;

    public function __construct($runtimeTask, $node)
    {
        $this->runtimeTask = $runtimeTask;
        $this->node = $node;
    }

    /**
     * Runing a command on remote server.
     * @param  string $commandline
     * @param  array  $options
     * @return ProcessResult
     */
    public function run($commandline, $options = array())
    {
        if (!$this->node) {
            throw new \RuntimeException("Node is not defined to run the command.");
        }

        if (is_array($commandline)) {
            $commandline = implode(" && ", $commandline);
        }

        $this->runtimeTask->getOutput()->writeln($this->getRemoteInfoPrefix()."<info>Run: </info>$commandline");

        $realCommand = $this->compileRealCommand($commandline, $options);

        if ($this->runtimeTask->getOutput()->isVeryVerbose()) {
            $this->runtimeTask->getOutput()->writeln($this->getRemoteInfoPrefix()."<info>Real command: </info>$realCommand");
        }

        $ssh = $this->getSSH();

        $self = $this;
        if (isset($options["timeout"])) {
            $ssh->setTimeout($options["timeout"]);
        } else {
            $ssh->setTimeout(null);
        }

        $resultContent = null;
        $ssh->exec($realCommand, function ($buffer) use ($self, &$resultContent) {
            $self->getRuntimeTask()->getOutput()->write($buffer);
            $resultContent .= $buffer;
        });

        $returnCode = $ssh->getExitStatus();
        return new ProcessResult($returnCode, $resultContent);
    }

    /**
     * Running a command on local machine.
     * @param  string $commandline
     * @param  array  $options
     * @return ProcessResult
     */
    public function runLocally($commandline, $options = array())
    {
        if (is_array($commandline)) {
            $os = php_uname('s');
            if(preg_match('/Windows/i', $os)){
                $commandline = implode(" & ", $commandline);
            }else {
                $commandline = implode(" && ", $commandline);
            }
        }

        $this->runtimeTask->getOutput()->writeln($this->getLocalInfoPrefix()."<info>Run: </info>$commandline");

        $realCommand = $this->compileRealCommand($commandline, $options, TRUE);

        if ($this->runtimeTask->getOutput()->isVeryVerbose()) {
            $this->runtimeTask->getOutput()->writeln($this->getLocalInfoPrefix()."<info>Real command: </info>$realCommand");
        }

        $self = $this;
        $symfonyProcess = new SymfonyProcess($realCommand);
        if (isset($options["timeout"])) {
            $symfonyProcess->setTimeout($options["timeout"]);
        } else {
            $symfonyProcess->setTimeout(null);
        }

        $resultContent = null;
        $returnCode = $symfonyProcess->run(function ($type, $buffer) use ($self, &$resultContent) {
            $self->getRuntimeTask()->getOutput()->write($buffer);
            $resultContent .= $buffer;
        });
        return new ProcessResult($returnCode, $resultContent);
    }

    protected function compileRealCommand($commandline, $options, $isLocalExecute=FALSE)
    {

        $realCommand = "";

        $os = php_uname('s');
        if($isLocalExecute && preg_match('/Windows/i', $os)){
            if (isset($options["user"])) {
                $realCommand .= 'runas /user:' . $options["user"] . ' ';
            }

            $realCommand .= 'cmd.exe /C "';

            if (isset($options["cwd"])) {
                $realCommand .= 'cd ' . $options["cwd"] . ' & ';
            }

            $realCommand .= str_replace('"', '\"', $commandline);
            $realCommand .= '"';
        }else {
            if (isset($options["user"])) {
                $realCommand .= 'sudo -H -u' . $options["user"] . ' TERM=dumb ';
            }

            $realCommand .= 'bash -l -c "';

            if (isset($options["cwd"])) {
                $realCommand .= 'cd ' . $options["cwd"] . ' && ';
            }

            $realCommand .= str_replace('"', '\"', $commandline);
            $realCommand .= '"';
        }

        return $realCommand;
    }

    protected function getSSH()
    {
        $output = $this->runtimeTask->getOutput();
        $input = $this->runtimeTask->getInput();

        $ssh = new \phpseclib\Net\SSH2(
            $this->node->getHostOrDefault(),
            $this->node->getPortOrDefault());

        // set up key
        $key = new \phpseclib\Crypt\RSA();

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

        $this->runtimeTask->getOutput()->writeln($this->getRemoteInfoPrefix()."<info>Get: </info>$remote -> $local");

        $sftp = $this->getSFTP();

        if (!is_dir(dirname($local))) {
            $fs = new Filesystem();
            $fs->mkdir(dirname($local));
            if ($this->runtimeTask->getOutput()->isVerbose()) {
                $this->runtimeTask->getOutput()->writeln($this->getRemoteInfoPrefix()."<info>Create directory: </info>".dirname($local));
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

        $this->runtimeTask->getOutput()->writeln($this->getRemoteInfoPrefix()."<info>Get: </info>$remote");

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

        $this->runtimeTask->getOutput()->writeln($this->getRemoteInfoPrefix()."<info>Put: </info>$local -> $remote");

        $sftp = $this->getSFTP();
        
        if (!is_file($local)) {
           throw new \RuntimeException("Couldn't put: $local -> $remote");
        }

        $ret = $sftp->put($remote, $local, \phpseclib\Net\SFTP::SOURCE_LOCAL_FILE);
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

        $this->runtimeTask->getOutput()->writeln($this->getRemoteInfoPrefix()."<info>Put: </info>$remote");

        $sftp = $this->getSFTP();
        $ret = $sftp->put($remote, $contents);
        if ($ret === false) {
            throw new \RuntimeException("Couldn't put: $remote");
        }

        return $ret;
    }

    protected function getSFTP()
    {
        $sftp = new \phpseclib\Net\SFTP(
            $this->node->getHostOrDefault(), 
            $this->node->getPortOrDefault());
        $key = new \phpseclib\Crypt\RSA();
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

    public function getRuntimeTask()
    {
        return $this->runtimeTask;
    }
}
