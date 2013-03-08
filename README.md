# Altax

This software is unstable release. You must not use it.

Altax is a simple deployment tool running SSH in parallel written in PHP.

# Requrement

* PHP5.3 or later.

# Installation

Just puts `altax` command file on your directory that is recommended to the path setting directory. (ex /usr/local/bin or /usr/bin .

Instead of manually, you can use quick install that is to run the following command on root user.

    $ curl https://raw.github.com/kohkimakimoto/altax/master/installer.sh | sh

# Usage

Runs altax init command like following.

    $ altax init

You will have default configuration file `altax.php`.

Modify `altax.php`.

Run following command to execute your task.

    $ altax TASK

See https://github.com/kohkimakimoto/altax/wiki You want to get more informations.

# License

  Apache License 2.0

# For developers

I use PHPCompactor(https://github.com/technosophos/PHPCompactor) to compact all PHP source files into one.

If you modify some source files in the altax/src directory, You need to run following command.

    $ php compile.php

This command generates altax command file from all PHP source files.

# Notice

I am only testing it on CentOS6, so perhaps it dosen't run on the other platforms.




