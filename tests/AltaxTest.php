<?php
class AltaxTest extends \PHPUnit_Framework_TestCase
{
  public function testMain()
  {
    $expect = <<<EOF

Altax is a simple deployment tool running SSH in parallel.

Altax version 1.3.0
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

  /*
  public function testExecute1()
  {
    $expect = <<<EOF

Available tasks :
  init  : Create default configuration file (altax.php).


EOF;

    $this->expectOutputString($expect);

    $altax = new Altax();
    $altax->execute(null,
      array('l' => null, 'f' => __DIR__."/AltaxTest/altax.php"),
      array());
  }
  */

  public function testExecute2()
  {
    $expect = <<<EOF

Altax is a simple deployment tool running SSH in parallel.

Altax version 1.3.0
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

    $altax = new Altax();
    $altax->execute(null,
        array('h' => null),
        array());
  }

  public function testExecute3()
  {
    $altax = new Altax();
    $altax->execute('init',
        array(),
        array());

    $this->assertEquals(true, is_file(__DIR__.'/../altax.php'));
    unlink(__DIR__.'/../altax.php');
  }
}