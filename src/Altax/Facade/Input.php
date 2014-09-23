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

    public static function getArguments()
    {
        if (staitc::hasArgument('args')
            && $args = staitc::getArgument('args')) {
            return $args;
        } else {
            return null;
        }
    }

    public static function getArgument($index = 0, $default = null)
    {
        $retVal = null;
        if ($args = staitc::getArguments()) {
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
