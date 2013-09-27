#!/bin/bash

PREFIX="/usr/local/bin/"
COMMAND_DIR=$(cd $(dirname $0);pwd)
TMPTIMESTAMP=`date +%y%m%d%H%M%S%N`
TMPDIR="altax.${TMPTIMESTAMP}.tmp"

install_altax() {

  type=$1

  if [ ! `which php` ]; then
    echo "For this installer to work you'll need to install PHP."
    echo '        http://php.net/'
    exit
  fi

  if [ ! `which git` ]; then
    echo "For this installer to work you'll need to install Git."
    echo '        http://git-scm.com/'
    exit
  fi

  git clone https://github.com/kohkimakimoto/altax.git ./${TMPDIR}
  cd ./${TMPDIR}

  php ./compile.php

  install_to="${COMMAND_DIR}/altax"
  if [ $type = "system" ]; then
    install_to="${PREFIX}altax"
  else
    install_to="${COMMAND_DIR}/altax"
  fi
  echo "Installing altax to ${install_to}"

  cp ./altax ${install_to}
  chmod 755 ${install_to}

  echo "Installed altax to ${install_to}"

  cd ..
  rm -rf ./${TMPDIR}
}

install_type="local"
install_version="1"

if [ $# -eq 1  ]; then
  if [ $1 = "system" ]; then
    install_type="system"
  fi
fi

if [ $# -eq 2  ]; then
  if [ $1 = "system" ]; then
    install_type="system"
  fi
  if [ $2 = "2" ]; then
    install_version="2"
  fi
fi

install_altax $install_type
