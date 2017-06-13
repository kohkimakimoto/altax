#!/bin/bash

PREFIX="/usr/local/bin/"
COMMAND_DIR=$(cd $(dirname $0);pwd)
TMPTIMESTAMP=`date +%y%m%d%H%M%S%N`
TMPDIR="altax.${TMPTIMESTAMP}.tmp"

install_altax() {

  type=$1
  version=$2

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

  echo "Thanks for installing altax!"
  echo "You will install altax version $version."
  echo "Now downloading."
  curl -s -o altax.${TMPTIMESTAMP}.phar https://raw.githubusercontent.com/kohkimakimoto/altax/$version/altax.phar
  chmod 755 altax.${TMPTIMESTAMP}.phar

  install_to="${COMMAND_DIR}/altax.phar"
  if [ $type = "system" ]; then
    install_to="${PREFIX}altax"
  else
    install_to="${COMMAND_DIR}/altax.phar"
  fi

  echo "Installing altax to ${install_to}"

  mv altax.${TMPTIMESTAMP}.phar ${install_to}

  echo "Done."
}

# default
install_type="local"
install_version="v3.0.15"

if [ $# -eq 1  ]; then
  if [ $1 = "system" ]; then
    install_type="system"
  fi
fi

if [ $# -eq 2  ]; then
  if [ $1 = "system" ]; then
    install_type="system"
  fi

  install_version=$2

  if [ $2 = "v2" ]; then
    # Recent version of v2
    install_version="v2.1.10"
  fi

  if [ $2 = "v3" ]; then
    # Recent version of v3
    install_version="v3.0.16"
  fi

fi

install_altax $install_type $install_version
