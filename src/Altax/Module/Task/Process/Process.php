<?php
namespace Altax\Module\Task\Process;

use Altax\Module\Server\Facade\Server;
use Altax\Module\Server\Resource\Node;
use Altax\Module\Task\Process\ProcessResult;
use Altax\Util\Arr;

class Process
{
    protected $node;

    public function __construct($node)
    {
        $this->node = $node;
    }

    public function run()
    {
        return new ProcessResult();
    }

    public function runLocally()
    {
        return new ProcessResult();
    }

}