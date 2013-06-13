<?php
class AltaxTest extends \PHPUnit_Framework_TestCase
{
  public function testMain()
  {
    $expect = <<<EOF

Altax is a simple deployment tool running SSH in parallel.

Altax version 1.2.0
Copyright (c) Kohki Makimoto <kohki.makimoto@gmail.com>
Apache License 2.0

Usage:
  altax [-d|-h|-f|-l|-c] TASK [ARGS..]

Options:
  -d         : Switch the debug mode to output log on the debug level.
  -h         : List available command line options (this page).
  -f=FILE    : Specify to load configuration file (default altax.php).
  -l         : List available tasks.
  -c         : List configurations.

Built-in tasks:
  init       : Create default configuration file (altax.php).


EOF;

    $this->expectOutputString($expect);
    Altax::main();
  }




}