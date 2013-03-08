#!/bin/bash

PREFIX="/usr/local/bin/"

install_altax() {

  if [ ! `which git` ]; then
    echo "For this installer to work you'll need to install Git."
    echo '        http://git-scm.com/'
  fi

  git clone https://github.com/kohkimakimoto/altax.git
  cd ./altax

  php ./compile.php
  cp ./altax ${PREFIX}
  chmod +x ${PREFIX}altax

}

install_altax
