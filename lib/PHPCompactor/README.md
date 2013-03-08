# PHPCompactor: A tool for compacting PHP source files

Authors: M Butcher (matt@aleph-null.tv), J Pruis (email@jurriaanpruis.nl)

Copyright (c) 2009-2010. Licensed under an MIT-style license. See COPYING-MIT.txt

## About this package

This package provides a very simple PHP code compressor. It reads a single source file and then loads that source, along with all of the other locally included files, into one bigger file. The larger file is compacted by removing as much superfluous data as possible, including comments and whitespace.

## Using this tool

Usage:

    php ./src/phpcompactor.php compressed_file.php source_file.php 
    
This will compress `source_file.php` and all of its dependencies into `compressed_file.php`.