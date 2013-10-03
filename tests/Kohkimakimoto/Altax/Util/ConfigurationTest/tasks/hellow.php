<?php
/**
 * Sample "Hellow" Task Class
 */
class HellowTask extends \Kohkimakimoto\Altax\Task\BaseTask
{
    public function configure()
    {
        return array(
            "name"        => "hellow",
            "description" => "echo hellow",
            "roles" => "web"
            );
    }

    public function execute($host, $args)
    {
        $this->run("echo Hellow on the $host");
    }
}