#!/bin/sh
#
# Deploy Drupal
# Author: Kevin Wenger
#
# Run as `./update.sh`
#

scriptDir=$( cd "$(dirname "${BASH_SOURCE}")" ; pwd -P )

composer install

#cd "web"

# Enable the maintenance on the Drupal project.
vendor/bin/drush state:set system.maintenance_mode 1 -y
vendor/bin/drush cr || true

  # Must updatedb before import configurations, E.g. when composer install new
  # version of Drupal and need updatedb scheme before importing new config.
  # This is executed without raise on error, because sometimes we need to do drush config-import before updatedb.
  vendor/bin/drush updatedb -y || true

  # Remove the cache after the database update
vendor/bin/drush cr

  # Sometimes Drupal import configs in wrong orders.
  # So we repeat the config-import max. 3 times on successive fails.
  n=0
  until [ $n -ge 3 ]; do
    vendor/bin/drush config-import -y
    n=$((n + 1))
    # delay 3s.
    sleep 3
  done

  # Update the database after configurations has been imported.
vendor/bin/drush updatedb -y

  # Clear your Drupal cache.
vendor/bin/drush cr

  # Disable the maintence on the Drupal project.
vendor/bin/drush state:set system.maintenance_mode 0 -y
vendor/bin/drush cr

# 192  sudo docker-compose exec dev vendor/bin/drush en jsonapi_explorer
# 193  sudo docker-compose exec dev vendor/bin/drush eshs
# 194  sudo docker-compose exec dev vendor/bin/drush eshd
# 195  sudo docker-compose exec dev vendor/bin/drush eshs
# 196  sudo docker-compose exec dev vendor/bin/drush eshd -y
# 197  sudo docker-compose exec dev vendor/bin/drush eshs
# 198  sudo docker-compose exec dev vendor/bin/drush eshr
# 199  sudo docker-compose exec dev vendor/bin/drush queue-run elasticsearch_helper_indexing
# 200  sudo docker-compose exec dev vendor/bin/drush cr
# 201  sudo docker-compose exec dev vendor/bin/drush cron
