#!/bin/sh
#
# Script to deploy docker based Games of Switzerland
# on successful build of master/dev branch
# Author: Kevin Wenger
#
# Run as `./deploy.sh [staging|production]`
#

scriptDir=$( cd "$(dirname "${BASH_SOURCE}")" ; pwd -P )

openssl aes-256-cbc -k $DEPLOY_KEY -in $TRAVIS_BUILD_DIR/config/deploy_id_rsa_enc_travis -d -a -out $TRAVIS_BUILD_DIR/config/deploy_id_rsa
gem install bundler
bundle install
bundle exec cap "$1" deploy
