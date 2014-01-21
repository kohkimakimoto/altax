#!/bin/sh 

sudo apt-get update -qq
sudo apt-get install -qq libssh2-1-dev openssh-client openssh-server

sudo start ssh

ssh-keygen -t rsa -f ~/.ssh/id_rsa -N "" -q
cat ~/.ssh/id_rsa.pub >>~/.ssh/authorized_keys
ssh-keyscan -t rsa localhost >>~/.ssh/known_hosts

export SSH_PRIVATE_KEY="$HOME/.ssh/id_rsa"
export SSH_PUBLIC_KEY="$HOME/.ssh/id_rsa.pub"

