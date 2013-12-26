<?php
namespace Kohkimakimoto\Altax\Task;

/**
 * @deprecated
 * @codeCoverageIgnore
 */
class BaseTask
{
    protected $options = null;

    protected function configure()
    {
        return array();
    }
    
    public function __construct($options = array())
    {
        $this->options = $options;
    }

    protected function execute($host, $args)
    {
    }

    public function dispatch($host, $args)
    {
        $this->execute($host, $args);
    }

    public function register()
    {
        $configure = $this->configure();

        $name = str_replace("\\", ":",strtolower(get_class($this)));
        if (isset($configure['name'])) {
            $name = $configure['name'];
            unset($configure['name']);
        }

        if (isset($configure['description'])) {
            desc($configure['description']);
            unset($configure['description']);
        }

        $options = $configure;
        $self = $this;
        task($name, $options, function($host, $args) use ($self){
            $self->dispatch($host, $args);
        });
    }

    protected function log($message)
    {
        message($message);
    }

    protected function run($command, $options = array())
    {
        run($command, $options);
    }

    protected function runTask($name, $args = array())
    {
        run_task($name);
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions($options)
    {
        $this->options = $options;
    }
}
