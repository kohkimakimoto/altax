<?php
namespace Kohkimakimoto\Altax\Task;

class BaseTask
{
    protected function configure()
    {
        return array();
        /*
        return array(
            "name" => "sample2"
            "description" => "sample2 taks description"
            "roles" => "web"

        );
        */
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
}
