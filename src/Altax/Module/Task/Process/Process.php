<?php
namespace Altax\Module\Task\Process;

use Symfony\Component\Process\Process as SymfonyProcess;
use Altax\Module\Server\Facade\Server;

class Process
{
    protected $runtimeTask;
    protected $commandline;
    protected $timeout;
    protected $isLocal;

    public function __construct($runtimeTask)
    {
        $this->runtimeTask = $runtimeTask;
        $this->commandline = null;
        $this->timeout = null;
        $this->isLocal = true;
    }

    public function setCommandline($commandline)
    {
        $this->commandline = $commandline;
    }

    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * Set roles or nodes to run process remotely
     * 
     * @return [type] [description]
     */
    public function on()
    {
        $candidateNodeNames = array();
        $concreteNodes = array();

        $args = func_get_args();
        if (count($args) === 0) {
            throw new \RuntimeException("Missing argument. Must 1 argument at minimum.");
        }

        if (count($args) === 1 && is_string($args[0])) {
            $candidateNodeNames = array($args[0]);
        }

        foreach ($candidateNodeNames as $candidateNodeName) {
            
            $node = Server::getNode($candidateNodeName);
            $role = Server::getRole($candidateNodeName);
            
            if ($node && $role) {
                throw new \RuntimeException("The key '$candidateNodeName' was found in both nodes and roles. So It couldn't identify to unique node.");
            }

            if ($node) {

            }

            print_r($node);

        } 

//        print_r($candidateNodeNames);

        return $this;
    }

    public function run()
    {
        if ($this->isLocal()) {
            // Runs process locally.
            $self = $this;
            $symfonyProcess = new SymfonyProcess($this->commandline);
            $symfonyProcess->setTimeout($this->timeout);
            $symfonyProcess->run(function ($type, $buffer) use ($self) {
                $self->runtimeTask->getOutput()->write($buffer);
            });

        } else {
            // Runs process remotely.

        }
    }

    public function isLocal()
    {
        return $this->isLocal;
    }

    public function isRemote()
    {
        return !$this->isLocal();
    }
}