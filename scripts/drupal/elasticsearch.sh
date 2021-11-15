#!/bin/sh
#
# Resetup the whole Elasticsearch process for Drupal
# Author: Kevin Wenger
#
# Run as `./elasticsearch.sh`
#


scriptDir=$( cd "$(dirname "${BASH_SOURCE}")" ; pwd -P )

./vendor/bin/drush eshd -y
./vendor/bin/drush eshs
./vendor/bin/drush eshr
./vendor/bin/drush queue-run elasticsearch_helper_indexing
./vendor/bin/drush cr
