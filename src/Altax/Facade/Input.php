<?php
namespace Altax\Facade;

class Input extends \Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'input'; }

    public function get($name)
    {
    }

    public function argument($name)
    {
    }

    public function option($name)
    {
    }

    protected static function getDefaultArguments()
    {
        if (static::hasArgument('args')
            && $args = static::getArgument('args')) {
            return $args;
        } else {
            return null;
        }
    }

    protected static function getDefaultArgument($index = 0, $default = null)
    {
        $retVal = null;
        if ($args = static::getDefaultArguments()) {
            if (isset($args[$index])) {
                $retVal = $args[$index];
            } else {
                $retVal = $default;
            }
        } else {
            $retVal = $default;
        }

        return $retVal;
    }


}
