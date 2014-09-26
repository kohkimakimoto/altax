<?php
namespace Altax\Process;

class Executor
{
    protected $app;

    protected $servers;

    protected $output;

    protected $console;

    public function __construct($app, $servers, $output, $console)
    {
        $this->app = $app;
        $this->servers = $servers;
        $this->output = $output;
        $this->console = $console;
    }

    public function exec()
    {
        $args = func_get_args();
        if (count($args) !== 2) {
            throw new \InvalidArgumentException("Missing argument. Must 2 arguments.");
        }

        $closure = null;
        $nodes = array();

        // load nodes
        if (is_string($args[0])) {
            $args[0] = array($args[0]);
        }
        $nodes = $this->servers->findNodes($args[0]);
        $closure = $args[1];

        if ($this->output->isDebug()) {
            $this->output->writeln("Found ".count($nodes)." nodes: "
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

        $processManager = new ProcessManager($closure, $nodes, $this->app, $this->output);
        $processManager->execute();
    }

    /**
     * Ask SSH key passphrase.
     * @return string passphrase
     */
    public function askPassphrase($validatingKey)
    {
        $dialog = $this->console->getHelperSet()->get('dialog');
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
