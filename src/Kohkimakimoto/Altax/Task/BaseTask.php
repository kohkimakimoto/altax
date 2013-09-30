<?php
namespace Kohkimakimoto\Altax\Task;

class BaseTask
{
    public function configure()
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

    public function execute($host, $args)
    {
    }

    public function register()
    {
        $configure = $this->configure();

        $name = strtolower(get_class($this));
        if (isset($configure['name'])) {
            $name = $configure['name'];
            unset($configure['name']);
        }

        if (isset($configure['description'])) {
            desc($configure['description']);
            unset($configure['description']);
        }

        $options = $configure;
        task($name, $options, array($this, "execute"));
    }
}
