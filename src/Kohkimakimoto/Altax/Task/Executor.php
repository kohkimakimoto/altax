<?php
namespace Kohkimakimoto\Altax\Task;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Kohkimakimoto\Altax\Util\Context;

class Executor
{
    protected $input;
    protected $output;

    protected $childPids = array();

    public function execute($taskName, InputInterface $input, OutputInterface $output, $parent = null)
    {
        $context = Context::getInstance();

        $this->input = $input;
        $this->output = $output;

        if (!function_exists('pcntl_fork')) {
            throw new \RuntimeException("Your PHP is not supported pcntl_fork function.");
        }

        $output->writeln("  - Starting task <info>$taskName</info>");
        
        $hosts = $this->getHosts($taskName);
        $output->write("    Found <info>".count($hosts)."</info> target hosts: ");
        foreach ($hosts as $i => $host) {
            if ($i == 0) {
                $output->write("<info>$host</info>");
            } else {
                $output->write("/<info>$host</info>");
            }
        }
        $output->writeln("");

        if ($context->get("debug") === true) {
            $output->writeln("    Setting up signal handler.");
        }

        pcntl_signal(SIGTERM, array($this, "signalHander"));
        pcntl_signal(SIGINT, array($this, "signalHander"));

        if ($context->get("debug") === true) {
            $output->writeln("    Processing to fork process.");
        }

        // Fork process.
        foreach ($hosts as $host) {
            $pid = pcntl_fork();
            if ($pid === -1) {
                // Error
                throw new \RuntimeException("Fork Error.");
            } else if ($pid) {
                // Parent process
                $this->childPids[$pid] = $host;
            } else {
                // child process
                if ($context->get("debug") === true) {
                    $output->writeln("    Forked child process: <info>$host</info> (<comment>pid:".posix_getpid()."</comment>)");
                }

                $task = new Task($taskName, $host, $input, $output);
                // Register current task.
                $context->set('currentTask', $task);
                // Execute task
                $task->execute();
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

            // At a child process finished, removes managed child pid.
            $host = $this->childPids[$pid];
            unset($this->childPids[$pid]);

            if ($context->get("debug") === true) {
               $output->writeln("    Finished child process: <info>$host</info> (<comment>pid:$pid</comment>)");
            }
        }

        $output->writeln("    Completed task <info>$taskName</info>");
    }

    protected function getHosts($taskName)
    {
        $context = Context::getInstance();

        // Get target hosts
        $hosts = $context->get('tasks/'.$taskName.'/options/hosts', array());
        if (is_string($hosts)) {
          $hosts = array($hosts);
        }

        // Get target hosts from roles
        $roles = $context->get('tasks/'.$taskName.'/options/roles', array());
        if (is_string($roles)) {
          $roles = array($roles);
        }

        foreach ($roles as $role) {
          // Get hosts related the role.
          $rhosts = $context->get('roles/'.$role, array());
          $hosts = array_merge($hosts, $rhosts);
        }

        return array_unique($hosts);
    }

    public function signalHander($signo)
    {
        // TODO: Impliment.
        switch ($signo) {
            case SIGTERM:
                $this->output->writeln("Got SIGTERM.");
                break;
            case SIGINT:
                $this->output->writeln("Got SIGINT.");
                break;
            default:
        }
    }
}