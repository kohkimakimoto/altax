#!/bin/bash

PREFIX="/usr/local/bin/"

install_altax() {

  if [ ! `which git` ]; then
    echo "For this installer to work you'll need to install Git."
    echo '        http://git-scm.com/'
  fi

  TMPTIMESTAMP=`date +%y%m%d%H%M%S%N`
  TMPDIR="altax.${TMPTIMESTAMP}.tmp"

  git clone https://github.com/kohkimakimoto/altax.git ./${TMPDIR}
  cd ./${TMPDIR}

  php ./compile.php
  cp ./altax ${PREFIX}altax
  chmod +x ${PREFIX}altax

  echo "Installed altax to ${PREFIX}altax"

  cd ..
  rm -rf ./${TMPDIR}


}

install_altax
