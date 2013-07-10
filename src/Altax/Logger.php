<?php
class Altax_Logger
{
  public static function log($msg, $prefix = null, $level = 'info')
  {
  	if (!Altax_Config::get('log', true)) {
      return;
    }

    if ($prefix) {
      $prefix .= ' ';
    }

    if ($level == 'debug') {
      if (Altax_Config::get('debug')) {
        if (Altax_Config::get('colors')) {
          echo pack('c',0x1B)."[0;32m"."[".date_create()->format('c')."] ".pack('c',0x1B)."[1;36mDEBUG ".$prefix.pack('c',0x1B)."[0m".$msg."\n";
        } else {
          echo "[".date_create()->format('c')."] DEBUG $prefix".$msg."\n";
        }
      }

    } else {
      if (Altax_Config::get('colors')) {
        echo pack('c',0x1B)."[0;32m"."[".date_create()->format('c')."] ".pack('c',0x1B)."[1;36m".$prefix.pack('c',0x1B)."[0m".$msg."\n";
      } else {
        echo "[".date_create()->format('c')."] $prefix".$msg."\n";
      }
    }
  }
}