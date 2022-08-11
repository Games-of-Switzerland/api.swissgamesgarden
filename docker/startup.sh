#!/bin/sh
#
# Docker "app" startup process.
# Author: Kevin Wenger
#
# Run as `./startup.sh`
#

# Export environment variable that may be used by Cron.
export > /var/www/.env.docker

# Start the Cron service.
service cron start

# Wait for DB Container to be ready then start Apache.
docker-as-wait db:3306 -- docker-as-drupal apache-server
