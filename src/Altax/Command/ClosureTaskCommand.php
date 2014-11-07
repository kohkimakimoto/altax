<?php
namespace Altax\Command;

use Symfony\Component\Console\Input\InputArgument;
use ReflectionFunction;

/**
 * Altax closure task command.
 */
class ClosureTaskCommand extends Command
{
    public function __construct($task)
    {
        if (!$task->hasClosure()) {
            throw new \RuntimeException("The task don't have a closure");
        }
        parent::__construct($task);
    }

    protected function configure()
    {
        $closure = $this->getClosure();
        $reflection = new ReflectionFunction($closure);
        $parameters = $reflection->getParameters();
        foreach ($parameters as $parameter) {
            $name = $parameter->getName();
            $isOptional = $parameter->isOptional();
            if ($isOptional) {
                $default = $parameter->getDefaultValue();
            } else {
                $default = null;
            }
            $this
                ->addArgument(
                    $name,
                    $isOptional ? InputArgument::OPTIONAL : InputArgument::REQUIRED,
                    null,
                    $default
                );
        }
    }

    protected function fire()
    {
        $closure = $this->getClosure();
        $reflection = new ReflectionFunction($closure);
        $parameters = $reflection->getParameters();
        $args = [];
        foreach ($parameters as $parameter) {
            $name = $parameter->getName();
            $args[] = $this->input->getArgument($name);
        }

        return call_user_func_array($closure, $args);
    }

    protected function getClosure()
    {
        return $this->task->getClosure();
    }
}
