<?php
namespace Altax\Process;

class ProcessManager
{
    protected $closure;

    protected $runtime;

    protected $output;

    protected $env;

    protected $isParallel;

    protected $childPids = array();

    public function __construct($closure, $runtime, $output, $env)
    {
        $this->closure = $closure;
        $this->runtime = $runtime;
        $this->output = $output;
        $this->env = $env;

        if ($this->env->get('process.parallel', true) == false) {
            $this->isParallel = false;
            if ($this->output->isDebug()) {
                $this->output->writeln("Running serial mode.");
            }
        } elseif (!function_exists('pcntl_signal') || !function_exists('pcntl_fork') || !function_exists('pcntl_wait') || !function_exists('posix_kill')) {
            $this->isParallel = false;
            if ($this->output->isDebug()) {
                $this->output->writeln("Running serial mode.");
            }
        } else {
            $this->isParallel = true;

            declare(ticks = 1);
            pcntl_signal(SIGTERM, array($this, "signalHandler"));
            pcntl_signal(SIGINT, array($this, "signalHandler"));
        }
    }

    public function executeWithNodes($nodes)
    {
        foreach ($nodes as $node) {
            if (!$this->isParallel) {
                $this->doExecuteWithNode($this->closure, $node);
                continue;
            }

            $pid = pcntl_fork();
            if ($pid === -1) {
                // Error
                throw new \RuntimeException("Fork Error.");
            } elseif ($pid) {
                // Parent process
                $this->childPids[$pid] = $node;
            } else {
                // Child process
                if ($this->output->isDebug()) {
                    $this->output->writeln("Forked process for node: ".$node->getName()." (pid:".posix_getpid().")");
                }

                $this->doExecuteWithNode($this->closure, $node);
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

            if ($this->output->isDebug()) {
                $this->output->writeln("Finished process for node: ".$node->getName()." (pid:".$pid.")");
            }

        }

    }

    public function executeWithEntries($entries)
    {
        foreach ($entries as $entry) {
            if (!$this->isParallel) {
                $this->doExecuteWithEntry($this->closure, $entry);
                continue;
            }

            $pid = pcntl_fork();
            if ($pid === -1) {
                // Error
                throw new \RuntimeException("Fork Error.");
            } elseif ($pid) {
                // Parent process
                $this->childPids[$pid] = $entry;
            } else {
                // Child process
                if ($this->output->isDebug()) {
                    $this->output->writeln("Forked process for entry: ".$entry." (pid:".posix_getpid().")");
                }

                $this->doExecuteWithEntry($this->closure, $entry);
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
            $entry = $this->childPids[$pid];
            unset($this->childPids[$pid]);

            if ($this->output->isDebug()) {
                $this->output->writeln("Finished process for node: ".$entry." (pid:".$pid.")");
            }

        }

    }

    protected function doExecuteWithNode($closure, $node)
    {
        $process = new NodeProcess($node);
        $this->runtime->setProcess($process);
        call_user_func($closure, $process);
        $this->runtime->backToMasterProcess();
    }

    protected function doExecuteWithEntry($closure, $entry)
    {
        $process = new SubProcess($entry);
        $this->runtime->setProcess($process);
        call_user_func($closure, $process);
        $this->runtime->backToMasterProcess();
    }

    public function signalHandler($signo)
    {
        switch ($signo) {
            case SIGTERM:
                $this->output->writeln("<fg=red>Got SIGTERM.</fg=red>");
                $this->killAllChildren();
                exit;

            case SIGINT:
                $this->output->writeln("<fg=red>Got SIGINT.</fg=red>");
                $this->killAllChildren();
                exit;
        }
    }

    public function killAllChildren()
    {
        foreach ($this->childPids as $pid => $host) {
            $this->output->writeln("<fg=red>Sending sigint to child (pid:</fg=red><comment>$pid</comment><fg=red>)</fg=red>");
            $this->killProcess($pid);
        }
    }

    protected function killProcess($pid)
    {
        posix_kill($pid, SIGINT);
    }

}
