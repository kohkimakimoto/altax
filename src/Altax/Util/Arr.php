<?php
namespace Altax\Util;

class Arr
{
    public static function isVector($array) {
        if (array_values($array) === $array) {
          return true;
        } else {
          return false;
        }
    }
}