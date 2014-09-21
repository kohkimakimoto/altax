<?php
namespace Altax\Task;

/**
 * Executor
 */
class Executor
{
    protected $runtimeTask;
    protected $closure;
    protected $options;
    protected $servers;
    protected $childPids = array();
    protected $isParallel;
    protected $keyPassphraseMap;

    public function __construct($runtimeTask, $closure, $options)
    {
        $this->runtimeTask = $runtimeTask;
        $this->closure = $closure;
        $this->options = $options;
        $this->servers = $runtimeTask->getTask()->getServers();
        $this->nodes   = $this->servers->findNodes($options);

        // Output info
        if ($this->runtimeTask->getOutput()->isVerbose()) {

            $this->runtimeTask->getOutput()
                ->writeln("<info>Found</info> <comment>"
                    .count($this->nodes)
                    ."</comment> nodes: "
                    ."".trim(implode(", ", array_keys($this->nodes))));
        }

        if (!function_exists('pcntl_signal') || !function_exists('pcntl_fork') || !function_exists('pcntl_wait') || !function_exists('posix_kill')) {
            $this->isParallel = false;
        } else {
            $this->isParallel = true;
        }
    }

    public function execute()
    {
        $nodes = $this->getNodes();

        foreach ($nodes as $node) {
            if (!$node->useAgent()
                && $node->isUsedWithPassphrase()
                && !$this->getKeyPassphraseMap()->hasPassphraseAtKey($node->getKeyOrDefault())
                ) {
                $passphrase = $this->askPassphrase($node->getKeyOrDefault());
                $this->getKeyPassphraseMap()->setPassphraseAtKey(
                    $node->getKeyOrDefault(),
                    $passphrase);
            }
        }

        // If target nodes count <= 1, It doesn't need to fork processes.
        if (count($nodes) === 0) {
            $this->doExecute(null);

            return;
        } elseif (count($nodes) === 1) {
            $this->doExecute(reset($nodes));

            return;
        }

        if (!$this->isParallel) {
            if ($this->runtimeTask->getOutput()->isVeryVerbose()) {
                $this->runtimeTask->getOutput()->writeln("<info>Running serial mode.</info>");
            }
            foreach ($nodes as $node) {
                $this->doExecute($node);
            }

            return;
        }

        // Fork process
        declare(ticks = 1);
        pcntl_signal(SIGTERM, array($this, "signalHandler"));
        pcntl_signal(SIGINT, array($this, "signalHandler"));

        foreach ($nodes as $node) {
            $pid = pcntl_fork();
            if ($pid === -1) {
                // Error
                throw new \RuntimeException("Fork Error.");
            } elseif ($pid) {
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

    public function getNodes()
    {
        return $this->nodes;
    }

    public function signalHandler($signo)
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
            $this->killProcess($pid);
        }
    }

    public function setIsParallel($isParallel)
    {
        $this->isParallel = $isParallel;
    }

    public function getIsParallel()
    {
        return $this->isParallel;
    }

    protected function killProcess($pid)
    {
        posix_kill($pid, SIGINT);
    }

    /**
     * Ask SSH key passphrase.
     * @return string passphrase
     */
    public function askPassphrase($validatingKey)
    {
        $output = $this->runtimeTask->getOutput();
        $command = $this->runtimeTask->getCommand();
        $dialog = $command->getHelperSet()->get('dialog');

        $passphrase = $dialog->askHiddenResponseAndValidate(
            $output,
            '<info>Enter passphrase for SSH key [<comment>'.$validatingKey.'</comment>]: </info>',
            function ($answer) use ($validatingKey) {

                $key = new \Crypt_RSA();
                $key->setPassword($answer);

                $keyFile = file_get_contents($validatingKey);
                if (!$key->loadKey($keyFile)) {
                    throw new \RuntimeException('wrong passphrase.');
                }

                return $answer;
            },
            3,
            null
        );

        return $passphrase;
    }
}
