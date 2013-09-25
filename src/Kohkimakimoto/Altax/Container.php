<?php
namespace Kohkimakimoto\Altax;

use Kohkimakimoto\Altax\Functions\Builtin;

/**
 * Altax Container
 * @author Kohki Makimoto <kohki.makimoto@gmail.com>
 */
class Container
{
    protected $configPath;

    function __construct($configPath)
    {
        $this->configPath = $configPath;
        require_once __DIR__."/Functions/builtin.php";
        
        include_once $this->configPath;
    }
}