# Altax

[![Build Status](https://travis-ci.org/kohkimakimoto/altax.png?branch=master)](https://travis-ci.org/kohkimakimoto/altax)
[![Coverage Status](https://coveralls.io/repos/kohkimakimoto/altax/badge.png?branch=master)](https://coveralls.io/r/kohkimakimoto/altax?branch=master)
[![Latest Stable Version](https://poser.pugx.org/kohkimakimoto/altax/v/stable.png)](https://packagist.org/packages/kohkimakimoto/altax)

**Altax version 3 is being developed now. You shouldn't use it.**

Altax is a deployment tool for PHP.
I designed it as a command-line tool for running tasks to remote servers 
like the [Capistrano](https://github.com/capistrano/capistrano) and [Cinamon](https://github.com/kentaro/cinnamon).
It also has a plugin mechanism for managing and installing tasks easily. 

This is a simple task definition. You cau use PHP.

```php
Server::node("web1.exsample.com", "web");
Server::node("web2.exsample.com", "web");
Server::node("db1.exsample.com",  "db");

Task::register("deploy", function($task){

    $appDir = "/path/to/app";

    $task->exec(function($process) use ($appDir){

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
[web1.exsample.com:8550] Run: test -d /var/tmp/altax
[web1.exsample.com:8550] Run: git clone git@github.com:kpath/to/app.git /path/to/app
Initialized empty Git repository in /path/to/app/.git/
[web2.exsample.com:8551] Run: test -d /var/tmp/altax
[web3.exsample.com:8551] Run: git clone git@github.com:kpath/to/app.git /path/to/app
Initialized empty Git repository in /path/to/app/.git/
```

## Requirement

PHP5.3 or later.

## Installation

There are several ways to install Altax to your system.
I recommend you to install it as a phar command file.
This is a most easy way.
Run the below command.

```Shell
$ curl -L https://raw.github.com/kohkimakimoto/altax/3.0/installer.sh | sudo bash -s system
```

You will get `altax` to `/usr/local/bin` directory. In order to check installation you execute just `altax` command.

```Shell
$ altax
Altax version 3.0.0

Altax is a deployment tool for PHP.
it's designed as a command-line tool for running tasks to remote servers.
Copyright (c) Kohki Makimoto <kohki.makimoto@gmail.com>
Apache License 2.0
...

```

## Usage

I describe a basic usage in this section.

Run `altax init` command to generate first configuration.

```Shell
[root@hakoniwa-dev01 work]# altax init
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

If you want to see more information, Visit a [documentation](http://kohkimakimoto.github.io/altax/) page.

## Documentation

See [altax project page](http://kohkimakimoto.github.io/altax/)

## Author 

Kohki Makimoto <kohki.makimoto@gmail.com>

## License

Apache License 2.0

See [LICENSE](./LICENSE)

## Previous version 

If you use Altax version 2. You can see **2.x branch**.
Altax version 1 is no longer maintained.

