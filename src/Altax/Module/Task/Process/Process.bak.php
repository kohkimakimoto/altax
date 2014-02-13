<?php
namespace Altax\Module\Task\Process;

use Symfony\Component\Process\Process as SymfonyProcess;
use Altax\Module\Server\Facade\Server;
use Altax\Module\Server\Resource\Node;
use Altax\Util\Arr;


class Process
{
    protected $runtimeTask;
    protected $commandline;
    protected $closure;
    protected $timeout;
    protected $nodes = array();
    protected $isAlreadyCalledOn = false;
    protected $isAlreadyCalledTo = false;
    protected $cwd = null;
    protected $user = null;

    protected $childPids = array();

    public function __construct($runtimeTask)
    {
        $this->runtimeTask = $runtimeTask;
        $this->commandline = null;
        $this->closure = null;
        $this->timeout = null;
    }

    public function setCommandline($commandline)
    {
        $this->commandline = $commandline;
    }

    public function getCommandline()
    {
        return $this->commandline;
    }

    public function setClosure($closure)
    {
        $this->closure = $closure;
    }

    public function getClosure()
    {
        return $this->closure;
    }

    public function hasClosure()
    {
        return isset($this->closure);
    }

    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    public function getTimeout()
    {
        return $this->timeout;
    }

    public function getRuntimeTask()
    {
        return $this->runtimeTask;
    }

    /**
     * Set roles or nodes for running process.
     * 
     * @return [type] [description]
     */
    public function to()
    {
        $nodes = call_user_func_array(array($this, "loadNodes"), func_get_args());

        $this->setNodes($nodes);

        // Output info
        if ($this->runtimeTask->getOutput()->isVerbose()) {

            $this->runtimeTask->getOutput()
                ->writeln("<info>Process#to set</info> <comment>"
                    .count($nodes)
                    ."</comment> nodes: "
                    ."".trim(implode(", ", array_keys($nodes))));
        }

        return $this;
    }

    public function cwd($cwd)
    {
        $this->cwd = $cwd;
        return $this;
    }

    public function user($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Load nods from variable length argument lists same 'on' and 'for' method.
     * @return array Array of Altax\Module\Server\Resource\Node
     */
    protected function loadNodes()
    {
        $candidateNodeNames = array();
        $concreteNodes = array();

        $args = func_get_args();

        if (count($args) === 0) {
            throw new \RuntimeException("Missing argument. Must 1 argument at minimum.");
        }

        foreach ($args as $arg) {
            if (is_string($arg)) {
                $candidateNodeNames[] = array(
                    "type" => null, // Means both node and role.
                    "name" => $arg,
                    );
            }

            if (is_array($arg)) {

                if (isset($arg["nodes"])) {
                    $nodes = array();
                    if (is_string($arg["nodes"])) {
                        $nodes[] = $arg["nodes"];
                    } elseif (is_array($arg["nodes"])) {
                        $nodes = $arg["nodes"];
                    }

                    foreach ($nodes as $node) {
                        $candidateNodeNames[] = array(
                            "type" => "node",
                            "name" => $node,
                        );
                    } 
                }

                if (isset($arg["roles"])) {
                    $roles = array();
                    if (is_string($arg["roles"])) {
                        $roles[] = $arg["roles"];
                    } elseif (is_array($arg["roles"])) {
                        $roles = $arg["roles"];
                    }

                    foreach ($roles as $role) {
                        $candidateNodeNames[] = array(
                            "type" => "role",
                            "name" => $role,
                        );
                    } 
                }

                if (Arr::isVector($arg)) {
                    foreach ($arg as $name) {
                        $candidateNodeNames[] = array(
                            "type" => null,
                            "name" => $name,
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

    /**
     * Run the process locally
     * @return [type] [description]
     */
    public function runLocally()
    {
        return $this->run(false);
    }


    /**
     * Run the process remotely.
     * @return [type] [description]
     */
    public function run($isRemote = true)
    {
        $nodes = $this->getNodes();
        if ($isRemote && count($nodes) === 0) {
            throw new \RuntimeException("Not found any remote node to connect.");
        }

        if (!$isRemote && count($nodes) === 0) {
            // Do not use parallel processing. (Do not use fork.)
            $this->doRunLocally();
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
                if ($this->runtimeTask->getOutput()->isVerbose()) {
                    $this->runtimeTask->getOutput()->writeln("<info>Forked process for node: </info>".$node->getName()." (pid:<comment>".posix_getpid()."</comment>)");
                }
                
                if ($isRemote) {
                    $this->doRunRemotely($node);
                } else {
                    $this->doRunLocally($node);
                }

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

    protected function doRunRemotely($node)
    {
        // Output info
        if ($this->runtimeTask->getOutput()->isVerbose()) {
            $this->runtimeTask->getOutput()->writeln("<info>Running process for </info><comment>remote</comment>.");
        }

        $ssh = new \Net_SSH2(
            $node->getHostOrDefault(),
            $node->getPortOrDefault());
        $key = new \Crypt_RSA();
        $key->loadKey(file_get_contents($node->getKeyOrDefault()));
        if (!$ssh->login($node->getUsernameOrDefault(), $key)) {
            throw new \RuntimeException('Unable to login '.$node->getName());
        }

        // Organize realcommand to run        
        $commandline = null;
        if ($this->hasClosure()) {
            $commandline .= call_user_func($this->closure, $node);
        } else {
            $commandline .= $this->commandline;
        }

        $commandline = $this->compileCommandline($commandline, $node);
        $realCommand = $this->compileRealCommand($commandline);

        if ($this->runtimeTask->getOutput()->isVerbose()) {
            $this->runtimeTask->getOutput()->writeln("<info>Real command: </info>$realCommand");
        }

        $self = $this;
        $ssh->exec($realCommand, function ($buffer) use ($self) {
            $self->getRuntimeTask()->getOutput()->write($buffer);
        });
    }

    /**
     * Run the process locally
     * @param  [type] $node [description]
     * @return [type]       [description]
     */
    protected function doRunLocally($node = null)
    {
        // Output info
        if ($this->runtimeTask->getOutput()->isVerbose()) {
            $this->runtimeTask->getOutput()->writeln("<info>Running process for </info><comment>local</comment>.");
        }

        // Organize realcommand to run
        $commandline = null;
        if ($this->hasClosure()) {
            $commandline .= call_user_func($this->closure, $node);
        } else {
            $commandline .= $this->commandline;
        }

        $commandline = $this->compileCommandline($commandline, $node);
        $realCommand = $this->compileRealCommand($commandline);

        if ($this->runtimeTask->getOutput()->isVerbose()) {
            $this->runtimeTask->getOutput()->writeln("<info>Real command: </info>$realCommand");
        }

        $self = $this;
        $symfonyProcess = new SymfonyProcess($realCommand);
        $symfonyProcess->setTimeout($this->timeout);
        $symfonyProcess->run(function ($type, $buffer) use ($self) {
            $self->getRuntimeTask()->getOutput()->write($buffer);
        });
    }

    public function compileCommandline($value, $node)
    {
        $pattern = '/{{\s*(.+?)\s*}}/s';
        $self = $this;
        $callback = function($matches) {
            return '<?php echo '.$matches[1].'; ?>';
        };

        $compiledValue = preg_replace_callback($pattern, $callback, $value);
        
        ob_start();
        eval(' ?>'.$compiledValue.'<?php ');
        $generatedValue = ob_get_contents();
        ob_end_clean();
        
        return $generatedValue;
    }

    public function compileRealCommand($value)
    {
        $realCommand = "";
        if ($this->user) {
            $realCommand .= 'sudo -u'.$this->user.' TERM=dumb ';
        }

        $realCommand .= '/bin/bash -l -c "';

        if ($this->cwd) {
            $realCommand .= 'cd '.$this->cwd.' && ';
        }

        $realCommand .= $value;
        $realCommand .= '"';

        return $realCommand;
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
                $this->runtimeTask->getOutput()->writeln("Got SIGTERM.");
                $this->killAllChildren();
                exit;

            case SIGINT:
                $this->runtimeTask->getOutput()->writeln("Got SIGINT.");
                $this->killAllChildren();
                exit;
        }
    }

    public function killAllChildren()
    {
        foreach ($this->childPids as $pid => $host) {
            $this->runtimeTask->getOutput()->writeln("Sending sigint to child (pid:<comment>$pid</comment>)");
            posix_kill($pid, SIGINT);
        }
    }

}