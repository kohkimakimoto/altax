# Altax

[![Build Status](https://travis-ci.org/kohkimakimoto/altax.png?branch=master)](https://travis-ci.org/kohkimakimoto/altax)
[![Coverage Status](https://coveralls.io/repos/kohkimakimoto/altax/badge.png?branch=master)](https://coveralls.io/r/kohkimakimoto/altax?branch=master)

Altax is a simple deployment tool for PHP. The features are the following.

* Written in PHP.
* Runs SSH in parallel.
* Easy to use. It runs in single PHP Archive(phar) file.

Altax is strongly inspired  (https://github.com/capistrano/capistrano) and [Cinamon](https://github.com/kentaro/cinnamon).
But these don’t allow you to write a deployment task in PHP. 
Altax is different. The following code is a example to declare deployment task for Altax.

```php
// Target hosts and ssh connection settings.
host('web1.exsample.com',  array('host' => '192.168.0.10', 'port' => '22'), 'web');
host('web2.exsample.com',  array('host' => '192.168.0.11', 'port' => '22'), 'web');
host('web3.exsample.com',  array('host' => '192.168.0.12', 'port' => '22'), 'web');

// Deployment task.
desc('Deploy application.');
task('deploy', array('roles' => 'web'), function($host, $args){

    run('git pull', array('cwd' => '/path/to/application'));

});
```

**Altax version 2 is being rebuilt using Symfony Components. It has a lot of difference from version 1.**
**If you use Altax version 1. You read [READNE.v1.md](./README.v1.md)**

## Requrement

PHP5.3 or later.

## Installation

Most easy way to install Altax to your system is to run the below command.

    $ curl https://raw.github.com/kohkimakimoto/altax/master/installer.sh | sudo bash -s system v2

You will get `altax` to `/usr/local/bin` directory.

Or, You can install it manually. Download [`altax.phar`](https://github.com/kohkimakimoto/altax/raw/master/altax.phar).
And move `altax.phar` to `/usr/local/bin`.

    $ wget https://github.com/kohkimakimoto/altax/raw/master/altax.phar
    $ chmod 755 altax.phar
    $ mv altax.phar /usr/local/bin/altax

## Basic Usage

Runs `altax init` command.

    $ altax init

You will have a default configuration file `.altax/config.php`.

Modify `.altax/config.php` for your environment. You need to define hosts and tasks like the following.

```php
<?php
host('127.0.0.1', array('web', 'localhost'));

desc('This is a sample task.');
task('sample',array('roles' => 'web'), function($host, $args){

    run('echo Hellow World!');

});
```

Run the following command to execute your sample task.

```
$ altax sample
Altax version 2.1.0
Starting altax process
  - Starting task sample
    Found 1 target hosts: 127.0.0.1
    - Running sample at 127.0.0.1
Hellow World!
    Completed task sample
```

## Configuration

Altax loads configuration files from  three different places.

* At first, loads `~/.altax/config.php` 
* At second, loads `.altax/config.php` under the currnt working directory.
* At third, loads file specified by a command line `-f` option.

Here is a sample configuration file.

```php
host('127.0.0.1', array('web', 'localhost'));

desc('This is a sample task.');
task('sample', function($host, $args){

  run('echo Hellow World!');

});
```

You can write any configuration in PHP. 
And you can use several configuration functions similar to Capistrano DSL.
Here is a list of Altax bultin configuration functions.

* **[host](#configuration-host)** - Associates a host with multiple roles.
* **[role](#configuration-role)** - Associates a role with multiple hosts.
* **task** - Defines a new task.
* **desc** - Associates a description with the next task that gets defined.
* **set** - Sets a variable.
* **get** - Gets a variable.
* **run** - Executes commands on remote managed server.
* **run_local** - Executes commands on local server.
* **run_task** - Runs other task in the task method.

### <a name ="configuration-host"> host

```php
host(string $host, [array $options,] mixed $roles)
```

**host** associates a host with multiple roles.
And configure specified host settings.
For instance SSH connection settings.

#### Parameters:

* `host`: Host name
* `options`: Associated settings to the host
* `roles`: Associated roles

#### Examples:

```php
// Define server "web1.exsample.com" and associates with "web" role.
host('web1.exsample.com', 'web');
// Define server "192.168.0.11" and associates with "web" and "dev" role.
host('192.168.0.11', array('web', 'dev'));
// Define server "web2.exsample.com" and associates with "web" role. options are ssh connection settings.
host('web2.exsample.com', array('host' => '192.168.0.12', 'port' => '22', 'login_name' => 'userhoge', 'identity_file' => '/home/userhoge/.ssh/id_rsa'), 'web');
```

### <a name ="configuration-role"> role

```php
role(string $role, mixed $hosts)
```

**role** associates a role with multiple hosts.

#### Parameters:

* `role`: Role name for classifing multiple hosts.
* `hosts`: Associated hosts.

#### Examples:

```php
role('web', array('web1.exsample.com','web2.exsample.com','web3.exsample.com'));
role('db', 'db1.exsample.com');
```

## License

Apache License 2.0






