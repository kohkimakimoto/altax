# Altax

[![Build Status](https://travis-ci.org/kohkimakimoto/altax.png?branch=master)](https://travis-ci.org/kohkimakimoto/altax)

Altax is a simple deployment tool running SSH in parallel. The features is the following.

* Written in PHP.
* Implemented as SSH command wrapper.
* If you use online installer, it runs in single PHP file.

## Documentations

https://github.com/kohkimakimoto/altax/wiki

## Requrement

* PHP5.3 or later.

## Installation

There are two way to install it.

### Using Online installer

You can use quick installation. Run the following command under the root user.

    # curl https://raw.github.com/kohkimakimoto/altax/master/installer.sh | bash -s system

You will get `altax` command in your `/usr/local/bin/`

### Using Composer

If you want to use composer to management packages, you can use composer installation.

Make `composer.json` like the following.

``` json
{
    "require": {
        "kohkimakimoto/altax": "dev-master"
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


