<?php
namespace Altax\Module\Task\Process;

use Altax\Module\Server\Facade\Server;
use Altax\Module\Server\Resource\Node;
use Altax\Util\Arr;

class ProcessResult
{
    protected $returnCode;
    protected $buffer;

    public function __construct($returnCode, $buffer)
    {
        $this->returnCode = $returnCode;
        $this->buffer = $buffer;
    }

    public function isFailed()
    {
        return !$this->isSuccessful();
    }

    public function isSuccessful()
    {
        if ($this->returnCode === 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getBuffer()
    {
        return $this->buffer;
    }

    public function __toString()
    {
        return $this->buffer;
    }
}