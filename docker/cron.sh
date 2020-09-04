#!/bin/bash
#
# Drupal Cron.
# Author: Kevin Wenger
#
# Run as `./cron.sh`
#

source /var/www/.env.docker
/var/www/vendor/bin/drush -r /var/www/web/ cron
