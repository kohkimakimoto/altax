# Altax

Altax is a simple deployment tool running SSH in parallel written in PHP.

* Run in single PHP file.
* Implemented as SSH command wrapper.

# Documentations

https://github.com/kohkimakimoto/altax/wiki

# Requrement

* PHP5.3 or later.

# Installation

You can use quick install that is to run the following command under the root user.

    $ curl https://raw.github.com/kohkimakimoto/altax/master/installer.sh | sh

You will get `altax` command in your `/usr/local/bin/`

# Usage

Runs Altax init command.

    $ altax init

You will have a default configuration file named `altax.php`.

Modify `altax.php` for your environment.

Run the following command to execute your task.

    $ altax TASK

See https://github.com/kohkimakimoto/altax/wiki You want to get more informations.

And My Blog post (written in Japanese)...

* http://kohkimakimoto.hatenablog.com/entry/2013/03/12/201330

# License

  Apache License 2.0

# For developers

I use PHPCompactor(https://github.com/technosophos/PHPCompactor) to compact all PHP source files into one.

If you modify some source files in the altax/src directory, You need to run following command.

    $ php compile.php

This command generates altax command file from all PHP source files.

# Notice

I like CentOS.

I am only testing it on CentOS6, so perhaps it dosen't run on the other platforms.




