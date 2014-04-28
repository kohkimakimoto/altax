# Altax

[![Build Status](https://travis-ci.org/kohkimakimoto/altax.png?branch=master)](https://travis-ci.org/kohkimakimoto/altax)
[![Coverage Status](https://coveralls.io/repos/kohkimakimoto/altax/badge.png?branch=master)](https://coveralls.io/r/kohkimakimoto/altax?branch=master)
[![Latest Stable Version](https://poser.pugx.org/kohkimakimoto/altax/v/stable.png)](https://packagist.org/packages/kohkimakimoto/altax)
[![License](https://poser.pugx.org/kohkimakimoto/altax/license.png)](https://packagist.org/packages/kohkimakimoto/altax)



Altax is a deployment tool for PHP.
I designed it as a command-line tool for running tasks to remote servers 
like the [Capistrano](https://github.com/capistrano/capistrano), [Fabric](http://fabric.readthedocs.org/) and [Cinamon](https://github.com/kentaro/cinnamon).
It also has a plugin mechanism for managing and installing tasks easily. 

This is a simple git deploy task definition. You can write any tasks in PHP.

```php
// Register managed nodes to a role.
Server::node("web1.example.com", "web");
Server::node("web2.example.com", "web");
Server::node("db1.example.com",  "db");

// Register a task.
Task::register("deploy", function($task){

    $appDir = "/path/to/app";

    // Execute parallel processes for each nodes.
    $task->exec(function($process) use ($appDir){

        // Run a command remotely and get a return code.
        if ($process->run("test -d $appDir")->isFailed()) {
            $process->run("git clone git@github.com:path/to/app.git $appDir");
        } else {
            $process->run(array(
                "cd $appDir",
                "git pull",
                ));
        }

    }, array("web"));

});

```

You can run it like below

```Shell
$ altax deploy
[web1.example.com:8550] Run: test -d /var/tmp/altax
[web1.example.com:8550] Run: git clone git@github.com:kpath/to/app.git /path/to/app
Initialized empty Git repository in /path/to/app/.git/
[web2.example.com:8551] Run: test -d /var/tmp/altax
[web3.example.com:8551] Run: git clone git@github.com:kpath/to/app.git /path/to/app
Initialized empty Git repository in /path/to/app/.git/
```

You can get more information at [http://kohkimakimoto.github.io/altax/](http://kohkimakimoto.github.io/altax/).

## Requirement

PHP5.3 or later.

## Installation

I recommend you to install Altax as a phar (PHP Archive) which compiled to single executable file.
Run the below command to get latest version of Altax.

```Shell
$ curl -L https://raw.githubusercontent.com/kohkimakimoto/altax/master/installer.sh | bash -s system
```

You will get `altax` command file to `/usr/local/bin` directory. In order to check installation,
execute just `altax` command.

```Shell
$ altax
Altax version 3.0.0

Altax is a extensible deployment tool for PHP.
Copyright (c) Kohki Makimoto <kohki.makimoto@gmail.com>
Apache License 2.0
...

```

## Usage

I describe basic usage in this section.

Run `altax init` command to generate first configuration.

```Shell
$ altax init
Created file: /path/to/your/directory/.altax/config.php
Created file: /path/to/your/directory/.altax/composer.json
Created file: /path/to/your/directory/.altax/.gitignore
```

Created `.altax/config.php` file in your current directory is a main configuration file for altax.
You can modify this file to define tasks and servers you managed.
So now, add the following code in the file.

```php
Task::register("hello", function($task){

  $task->writeln("Hello world!");

})->description("This is a first sample task.");
```

This is a simple task definition. Defined task is listed by executing just `altax` command.

```Shell
$ altax
Altax version 3.0.0

Altax is a deployment tool for PHP.
it's designed as a command-line tool for running tasks to remote servers.
Copyright (c) Kohki Makimoto <kohki.makimoto@gmail.com>
Apache License 2.0

...

Available commands:
  hello   This is a first sample task.
  ...
```

`hello` task you defined can be executed by `altax` command with task name like the followiing.

```Shell
$ altax hello
Hello world!
``` 

You got a first altax task now!

If you want to see more information, visit a [documentation](http://kohkimakimoto.github.io/altax/) page.

## Documentation

See [documentation](http://kohkimakimoto.github.io/altax/) page.

## Plugins 

Altax has a extensible plugin mechanism. It makes adding functionality easy.
Plugins are stored at [Packagist](https://packagist.org/) and installed using [composer](https://getcomposer.org/).
As Altax includes embedded composer, you can install plugins by altax command. 

For instance, if you use PHP5.4 and MySQL database in your product, you can use [Adminer](http://www.adminer.org/) database management tool via Altax plugin.
Edit your `.altax/composer.json` file like the following.

```json
{
  "require": {
    "kohkimakimoto/altax-adminer": "dev-master"
  }
}
```

And run altax update command which is a wrapper command of `composer update` for Altax.

```Shell
$ altax update
```

Adminer altax plugin will be installed in your `.altax/vendor` directory.
In order to register the plugin to your task, add the following line your `.altax/config.php` file.

```php
Task::register('adminer', 'Altax\Contrib\Adminer\Command\AdminerCommand');
```

Run the registered plugin task commnad.

```Shell
$ altax adminer
```

Altax runs adminer on built-in web server. So you can use adminer at `http://localhost:3000/`.

If you are interested in Altax plugins, [Search plugins at packagist](https://packagist.org/search/?q=altax)!

## Author 

Kohki Makimoto <kohki.makimoto@gmail.com>

## License

Apache License 2.0

See [LICENSE](./LICENSE)

## Previous version 

If you use Altax version 2. You can see **[2.x branch](https://github.com/kohkimakimoto/altax/tree/2.x)**.
Altax version 1 is no longer maintained.

