<?php
namespace Altax\Process;

class Executor
{
    protected $runtime;

    protected $servers;

    protected $output;

    protected $console;

    protected $env;

    public function __construct($runtime, $servers, $output, $console, $env)
    {
        $this->runtime = $runtime;
        $this->servers = $servers;
        $this->output = $output;
        $this->console = $console;
        $this->env = $env;
    }

    public function on($options, $closure)
    {
        $nodes = array();

        if (is_string($options)) {
            $options = array($options);
        }

        $nodes = $this->servers->findNodes($options);

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

        $manager = new ProcessManager($closure, $this->runtime, $this->output, $this->env);
        $manager->executeWithNodes($nodes);
    }

    public function exec($options, $closure)
    {
        $entries = array();

        if (is_string($options)) {
            $options = array($options);
        }

        if (is_vector($options)) {
            $entries = $options;
        } else {
            if (isset($options['entries']) && is_array($options['entries'])) {
                $entries = $options['entries'];
            }
        }

        if ($this->output->isDebug()) {
            $this->output->writeln("Found ".count($entries)." entries: "
                ."".trim(implode(", ", array_keys($entries))));
        }

        if (!($closure instanceof \Closure)) {
            throw new \InvalidArgumentException("You must pass a closure.");
        }

        $manager = new ProcessManager($closure, $this->runtime, $this->output, $this->env);
        $manager->executeWithEntries($entries);
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
