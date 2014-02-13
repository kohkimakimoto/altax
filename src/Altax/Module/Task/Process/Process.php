<?php
namespace Altax\Module\Task\Process;

use Symfony\Component\Process\Process as SymfonyProcess;
use Altax\Module\Server\Facade\Server;
use Altax\Module\Server\Resource\Node;
use Altax\Module\Task\Process\ProcessResult;
use Altax\Util\Arr;

class Process
{
    protected $node;
    protected $runtimeTask;

    public function __construct($runtimeTask, $node)
    {
        $this->runtimeTask = $runtimeTask;
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

        // Output info
        if ($this->runtimeTask->getOutput()->isVerbose()) {
            $this->runtimeTask->getOutput()->writeln($this->getRemoteInfoPrefix()."<info>Run: </info>$commandline");
        }

        $realCommand = $this->compileRealCommand($commandline, $options);

        if ($this->runtimeTask->getOutput()->isVeryVerbose()) {
            $this->runtimeTask->getOutput()->writeln($this->getRemoteInfoPrefix()."<info>Real command: </info>$realCommand");
        }

        $ssh = new \Net_SSH2(
            $this->node->getHostOrDefault(),
            $this->node->getPortOrDefault());
        $key = new \Crypt_RSA();
        $key->loadKey(file_get_contents($this->node->getKeyOrDefault()));
        if (!$ssh->login($this->node->getUsernameOrDefault(), $key)) {
            throw new \RuntimeException('Unable to login '.$this->node->getName());
        }

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

    public function runLocally($commandline, $options = array())
    {
        if (is_array($commandline)) {
            $commandline = implode(" && ", $commandline);
        }

        // Output info
        if ($this->runtimeTask->getOutput()->isVerbose()) {
            $this->runtimeTask->getOutput()->writeln($this->getLocalInfoPrefix()."<info>Run: </info>$commandline");
        }

        $realCommand = $this->compileRealCommand($commandline, $options);

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

        $realCommand .= $commandline;
        $realCommand .= '"';

        return $realCommand;
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
        return "<info>[</info><comment>".$this->getNodeName().":".posix_getpid()."</comment><info>]</info> ";
    }

    public function getLocalInfoPrefix()
    {
        return "<info>[</info><comment>localhost:".posix_getpid()."</comment><info>]</info> ";
    }

    public function getRuntimeTask()
    {
        return $this->runtimeTask;
    }
}