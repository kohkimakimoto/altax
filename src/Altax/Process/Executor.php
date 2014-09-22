<?php
namespace Altax\Process;

class Executor
{
    protected $servers;

    protected $output;

    protected $command;

    protected $isParallel;

    protected $childPids = array();

    public function __construct($servers, $output, $command)
    {
        $this->servers = $servers;
        $this->output = $output;
        $this->command = $command;

        if (!function_exists('pcntl_signal') || !function_exists('pcntl_fork') || !function_exists('pcntl_wait') || !function_exists('posix_kill')) {
            $this->isParallel = false;
        } else {
            $this->isParallel = true;
        }
    }

    public function exec()
    {
        $args = func_get_args();
        if (count($args) === 0) {
            throw new \InvalidArgumentException("Missing argument. Must 1 arguments at minimum.");
        }

        $closure = null;
        $nodes = array();

        // load nodes
        if (count($args) === 1) {
            // Passed only a closure.
            $closure = $args[0];
        } elseif (count($args) === 2) {
            // Passed with target nodes or roles.
            if (is_string($args[0])) {
                $args[0] = array($args[0]);
            }
            $nodes = $this->servers->findNodes($args[0]);
            $closure = $args[1];
        }

        if ($this->output->isDebug()) {
            $this->output->writeln("<comment>[debug]</comment> Found ".count($nodes)." nodes: "
                ."".trim(implode(", ", array_keys($nodes))));
        }

        if (!($closure instanceof \Closure)) {
            throw new \InvalidArgumentException("You must pass a closure.");
        }

        // check ssh keys
        foreach ($nodes as $node) {
            if (!$node->useAgent()
                && $node->isUsedWithPassphrase()
                && !$this->servers->getKeyPassphraseMap()->hasPassphraseAtKey($node->getKeyOrDefault())
                ) {
                $passphrase = $this->askPassphrase($node->getKeyOrDefault());
                $this->servers->getKeyPassphraseMap()->setPassphraseAtKey(
                    $node->getKeyOrDefault(),
                    $passphrase);
            }
        }

        // If target nodes count <= 1, It doesn't need to fork processes.
        if (count($nodes) <= 1) {
            $this->doExecute($closure, null);

            return;
        }

        if (!$this->isParallel) {
            if ($this->output->isDebug()) {
                $this->output->writeln("<comment>[debug]</comment> <info>Running serial mode.</info>");
            }
            foreach ($nodes as $node) {
                $this->doExecute($closure, $node);
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
                if ($this->output->isDebug()) {
                    $this->output->writeln("<comment>[debug]</comment> <info>Forked process for node: </info>".$node->getName()." (pid:<comment>".posix_getpid()."</comment>)");
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
        }

    }

    protected function doExecute($closure, $node)
    {
        call_user_func($closure, new Process($this->output, $this, $node));
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
        $dialog = $this->command->getHelperSet()->get('dialog');
        $passphrase = $dialog->askHiddenResponseAndValidate(
            $this->output,
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
