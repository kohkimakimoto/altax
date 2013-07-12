# Altax

[![Build Status](https://travis-ci.org/kohkimakimoto/altax.png?branch=master)](https://travis-ci.org/kohkimakimoto/altax)

Altax is a simple deployment tool running SSH in parallel. The features are the following.

* Written in PHP.
* Implemented as SSH command wrapper.
* If you use compiled package. It runs in single PHP file.

More infomations are in a [project page](http://kohkimakimoto.github.io/altax/).

## Requrement

* PHP5.3 or later.

## Installation


### Installing compiled package

The compiled package is a single executable PHP file generated from all Altax src files.

    # curl https://raw.github.com/kohkimakimoto/altax/master/installer.sh | bash -s system

You will get `altax` command in your `/usr/local/bin/`

### Installing composer package

[Composer](http://getcomposer.org/) is a famous dependency management tool for PHP.
If you want to use composer to manage packages, you can use composer installation.
Make `composer.json` file like the following.


``` json
{
    "require": {
        "kohkimakimoto/altax": "~1.3.0"
    }
}
```

And run Composer install command.

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar install

More information of composer is in [http://getcomposer.org/](http://getcomposer.org/).

## Usage

Runs Altax init command.

    $ altax init

You will have a default configuration file named `altax.php`.

Modify `altax.php` for your environment. You need to define hosts and tasks like the following.

``` php
<?php
host('192.168.0.1', 'web');
host('192.168.0.2', 'web');

desc('This is a sample task.');
task('sample',array('roles' => 'web'), function($host, $args){

  run('echo Hellow World!');

});
```

Run the following command to execute your sample task.

    $ altax sample

See https://github.com/kohkimakimoto/altax/wiki You want to get more informations.

And My Blog post (written in Japanese)...

* http://kohkimakimoto.hatenablog.com/entry/2013/03/12/201330

## License

  Apache License 2.0


