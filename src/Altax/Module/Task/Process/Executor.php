<?php
namespace Altax\Module\Task\Process;

use Altax\Module\Server\Facade\Server;
use Altax\Module\Server\Resource\Node;
use Altax\Module\Task\Process\Process;
use Altax\Util\Arr;


class Executor
{
    protected $runtimeTask;
    protected $closure;
    protected $options;
    protected $childPids;

    public function __construct($runtimeTask, $closure, $options)
    {
        $this->runtimeTask = $runtimeTask;
        $this->closure = $closure;
        $this->options = $options;
        $this->nodes   = $this->loadNodes($options);

        // Output info
        if ($this->runtimeTask->getOutput()->isVerbose()) {

            $this->runtimeTask->getOutput()
                ->writeln("<info>Found</info> <comment>"
                    .count($this->nodes)
                    ."</comment> nodes: "
                    ."".trim(implode(", ", array_keys($this->nodes))));
        }

    }

    public function execute()
    {
        $nodes = $this->getNodes();
        if (count($nodes) === 0) {
            $this->doExecute(null);
            return;
        }

        // Fork process
        declare(ticks = 1);
        pcntl_signal(SIGTERM, array($this, "signalHander"));
        pcntl_signal(SIGINT, array($this, "signalHander"));

        foreach ($nodes as $node) {
            $pid = pcntl_fork();
            if ($pid === -1) {
                // Error
                throw new \RuntimeException("Fork Error.");
            } else if ($pid) {
                // Parent process
                $this->childPids[$pid] = $node;
            } else {
                // Child process
                if ($this->runtimeTask->getOutput()->isVeryVerbose()) {
                    $this->runtimeTask->getOutput()->writeln("<info>Forked process for node: </info>".$node->getName()." (pid:<comment>".posix_getpid()."</comment>)");
                }
                
                $this->doExecute($node);
                exit(0);
            }
        }

        // At the following code, only parent precess runs.
        while (count($this->childPids) > 0) {
            // Keep to wait until to finish all child processes.
            $status = null;
            $pid = pcntl_wait($status);
            if (!$pid) {
                throw new \RuntimeException("pcntl_wait error.");
            }

            if (!array_key_exists($pid, $this->childPids)) {
                throw new \RuntimeException("pcntl_wait error.".$pid);
            }

            // When a child process is done, removes managed child pid.
            $node = $this->childPids[$pid];
            unset($this->childPids[$pid]);
        }
    }

    protected function doExecute($node)
    {
        call_user_func($this->closure, new Process($this->runtimeTask, $node));
    }

    /**
     * Load nods from variable length argument lists same 'on' and 'for' method.
     * @return array Array of Altax\Module\Server\Resource\Node
     */
    protected function loadNodes(array $args)
    {
        $candidateNodeNames = array();
        $concreteNodes = array();

        if (Arr::isVector($args)) {
            foreach ($args as $arg) {
                if (is_string($arg)) {
                    $candidateNodeNames[] = array(
                        "type" => null, // Means both node and role.
                        "name" => $arg,
                        );
                }
            }
        } else {
            foreach ($args as $key => $value) {
                if ($key == "nodes" || $key == "node") {
                    $nodes = array();
                    if (is_string($value)) {
                        $nodes[] = $value;
                    } elseif (is_array($value)) {
                        $nodes = $value;
                    }
                    foreach ($nodes as $node) {
                        $candidateNodeNames[] = array(
                            "type" => "node",
                            "name" => $node,
                        );
                    }
                }
                if ($key == "roles" || $key == "role") {
                    $roles = array();
                    if (is_string($value)) {
                        $roles[] = $value;
                    } elseif (is_array($value)) {
                        $roles = $value;
                    }
                    foreach ($roles as $role) {
                        $candidateNodeNames[] = array(
                            "type" => "role",
                            "name" => $role,
                        );
                    }
                }
            }

        }

        foreach ($candidateNodeNames as $candidateNodeName) {
            
            $node = null;
            $role = null;

            if ($candidateNodeName["type"] === null || $candidateNodeName["type"] == "node") {
                $node = Server::getNode($candidateNodeName["name"]);
            }

            if ($candidateNodeName["type"] === null || $candidateNodeName["type"] == "role") {
                $role = Server::getRole($candidateNodeName["name"]);
            }
            
            if ($node && $role) {
                throw new \RuntimeException("The key '".$candidateNodeName["name"]."' was found in both nodes and roles. So It couldn't identify to unique node.");
            }

            if (!$node && !$role && ($candidateNodeName["type"] === null || $candidateNodeName["type"] == "node")) {
                // Passed unregisterd node name. Create node instance.
                $node = new Node();
                $node->setName($candidateNodeName["name"]);
            }

            if ($node) {
                $concreteNodes[$node->getName()] = $node;
            }

            if ($role) {
                foreach($role as $nodeName) {
                    $concreteNodes[$nodeName] = Server::getNode($nodeName);
                }
            }
        }

        return $concreteNodes;
    }


    public function setNodes($nodes)
    {
        $this->nodes = $nodes;
    }

    public function getNodes()
    {
        return $this->nodes;
    }

    public function signalHander($signo)
    {
        switch ($signo) {
            case SIGTERM:
                $this->runtimeTask->getOutput()->writeln("<fg=red>Got SIGTERM.</fg=red>");
                $this->killAllChildren();
                exit;

            case SIGINT:
                $this->runtimeTask->getOutput()->writeln("<fg=red>Got SIGINT.</fg=red>");
                $this->killAllChildren();
                exit;
        }
    }

    public function killAllChildren()
    {
        foreach ($this->childPids as $pid => $host) {
            $this->runtimeTask->getOutput()->writeln("<fg=red>Sending sigint to child (pid:</fg=red><comment>$pid</comment><fg=red>)</fg=red>");
            posix_kill($pid, SIGINT);
        }
    }

}