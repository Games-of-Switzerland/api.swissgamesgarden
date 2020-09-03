#!/bin/sh
#
# Script to deploy docker based Games of Switzerland
# on successful build of master/dev branch
# Author: Kevin Wenger
#
# Run as `./deploy.sh [staging|production]`
#

scriptDir=$( cd "$(dirname "${BASH_SOURCE}")" ; pwd -P )

bundle install
bundle exec cap "$1" deploy
