# Altax

[![Build Status](https://travis-ci.org/kohkimakimoto/altax.png?branch=master)](https://travis-ci.org/kohkimakimoto/altax)
[![Coverage Status](https://coveralls.io/repos/kohkimakimoto/altax/badge.png?branch=master)](https://coveralls.io/r/kohkimakimoto/altax?branch=master)
[![Latest Stable Version](https://poser.pugx.org/kohkimakimoto/altax/v/stable.png)](https://packagist.org/packages/kohkimakimoto/altax)
[![License](https://poser.pugx.org/kohkimakimoto/altax/license.png)](https://packagist.org/packages/kohkimakimoto/altax)



Altax is a deployment tool for PHP.
I designed it as a command-line tool for running tasks to remote servers 
like the [Capistrano](https://github.com/capistrano/capistrano), [Fabric](http://fabric.readthedocs.org/) and [Cinamon](https://github.com/kentaro/cinnamon).

The following code is a simple git deploy task definition. You can write any tasks in PHP.

```php
// Register managed nodes to a role.
Server::node("web1.example.com", "web");
Server::node("web2.example.com", "web");
Server::node("db1.example.com",  "db");

// Register a task.
Task::register("deploy", function(){

    $appDir = "/path/to/app";

    // Execute parallel processes for each nodes.
    Process::exec(["web", "db"], function() use ($appDir){

        // Run a command remotely and get a return code.
        if (Command::run("test -d $appDir")->isFailed()) {
            Command::run("git clone git@github.com:path/to/app.git $appDir");
        } else {
            Command::run(array(
                "cd $appDir",
                "git pull",
                ));
        }

    });

});

```

You can run it like below

```
$ altax deploy
Run command: test -d /path/to/app on web1.example.com
Run command: git clone git@github.com:path/to/app.git /path/to/app on web1.example.com
Run command: test -d /path/to/app on web2.example.com
Run command: git clone git@github.com:path/to/app.git /path/to/app on web2.example.comInitialized empty Git repository in /path/to/app/.git/
Run command: test -d /path/to/app on db1.example.com
Run command: git clone git@github.com:path/to/app.git /path/to/app on db1.example.com
```

You can get more information at [http://kohkimakimoto.github.io/altax/](http://kohkimakimoto.github.io/altax/).

## Requirement

PHP5.4 or later.

(Altax version 3.x requires PHP5.3 later)

## Installation


## Author

Kohki Makimoto <kohki.makimoto@gmail.com>

## License

Apache License 2.0

See [LICENSE](./LICENSE)

