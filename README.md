# Altax

[![Build Status](https://travis-ci.org/kohkimakimoto/altax.png?branch=master)](https://travis-ci.org/kohkimakimoto/altax)
[![Coverage Status](https://coveralls.io/repos/kohkimakimoto/altax/badge.png?branch=master)](https://coveralls.io/r/kohkimakimoto/altax?branch=master)

Altax is a simple deployment tool running SSH in parallel. The features are the following.

* Written in PHP.
* Implemented as SSH command wrapper.
* If you use compiled package (phar file). It runs in single PHP file.

**Altax Version 2 is being rebuilt using Symfony Components. It has a lot of difference from version 1.**
**If you use Altax version 1. You read [READNE.v1.md](./README.v1.md)**

## Requrement

PHP5.3 or later.

## Installation

Just download [`altax.phar`](https://github.com/kohkimakimoto/altax/raw/master/altax.phar).
And move `altax.phar` to `/usr/local/bin/altax`.

    $ wget https://github.com/kohkimakimoto/altax/raw/master/altax.phar
    $ chmod 755 altax.phar
    $ mv altax.phar /usr/local/bin/altax

## Usage

Runs `altax init` command.

    $ altax init

You will have a default configuration file named `./altax/config.php`.

Modify `./altax/config.php` for your environment. You need to define hosts and tasks like the following.

    <?php
    host('192.168.0.1', 'web');
    host('192.168.0.2', 'web');

    desc('This is a sample task.');
    task('sample',array('roles' => 'web'), function($host, $args){

      run('echo Hellow World!');

    });

Run the following command to execute your sample task.

    $ altax sample

## License

Apache License 2.0






