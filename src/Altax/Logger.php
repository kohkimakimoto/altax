<?php
class Altax_Logger
{
  public static function log($msg, $level = 'info')
  {
  	if (!Altax_Config::get('log', true)) {
      return;
    }

    if ($level == 'debug') {
      if (Altax_Config::get('debug')) {
        echo "[".date_create()->format('c')."] DEBUG ".$msg."\n";
      }
    } else {
      echo "[".date_create()->format('c')."] INFO ".$msg."\n";
    }
  }
}