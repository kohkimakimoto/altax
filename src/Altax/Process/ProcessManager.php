<?php
namespace Altax\Process;

class ProcessManager
{
    protected $runtime;

    protected $output;

    protected $isParallel;

    protected $childPids = array();

    public function __construct($runtime, $output)
    {
        $this->runtime = $runtime;
        $this->output = $output;

        if (!function_exists('pcntl_signal') || !function_exists('pcntl_fork') || !function_exists('pcntl_wait') || !function_exists('posix_kill')) {
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

    public function execute($closure, $nodes)
    {
        foreach ($nodes as $node) {
            if (!$this->isParallel) {
                $this->doExecute($closure, $node);
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

                $this->doExecute($closure, $node);
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

    protected function doExecute($closure, $node)
    {
        $process = new Process($node);
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
