# Altax

[![Build Status](https://travis-ci.org/kohkimakimoto/altax.png?branch=3.0)](https://travis-ci.org/kohkimakimoto/altax)
[![Coverage Status](https://coveralls.io/repos/kohkimakimoto/altax/badge.png?branch=3.0)](https://coveralls.io/r/kohkimakimoto/altax?branch=3.0)
[![Latest Stable Version](https://poser.pugx.org/kohkimakimoto/altax/v/stable.png)](https://packagist.org/packages/kohkimakimoto/altax)

**Altax version 3 is being developed now. You shouldn't use it.**

A simple deployment tool for PHP.

## Overview

Altax is a command-line tool for running tasks to remote servers.
It's strongly inspired [Capistrano](https://github.com/capistrano/capistrano) and [Cinamon](https://github.com/kentaro/cinnamon).

## Requirement

PHP5.3 or later.

## Installation

There are several ways to install Altax to your system.

### Installing as a phar (Most easy way)

Most easy way to install Altax to your system is to run the below command.

    $ curl -L https://raw.github.com/kohkimakimoto/altax/3.0/installer.sh | sudo bash -s system v3

You will get `altax` to `/usr/local/bin` directory.

Or, You can install it manually. Download [`altax.phar`](https://github.com/kohkimakimoto/altax/raw/master/altax.phar).
And move `altax.phar` to `/usr/local/bin`.

    $ curl -L -O https://github.com/kohkimakimoto/altax/raw/3.0/altax.phar
    $ chmod 755 altax.phar
    $ mv altax.phar /usr/local/bin/altax

### Installing as a composer package

[Composer](http://getcomposer.org/) is a famous dependency management tool for PHP. 
If you want to use Altax through a composer package management, 
you can use composer installation. 
Make `composer.json` file like the following.

```json
{
  "require": {
    "kohkimakimoto/altax": "3.*"
  }
}
```
And run composer install command.

```
$ curl -s http://getcomposer.org/installer | php
$ php composer.phar install
```

## Documentation

Visit [altax project page](http://kohkimakimoto.github.io/altax/)

## Author 

Kohki Makimoto <kohki.makimoto@gmail.com>

## License

Apache License 2.0

See [LICENSE](./LICENSE)

## Previous version 

If you use Altax version 2. You can see **2.x branch**.
Altax version 1 is no longer maintained.

