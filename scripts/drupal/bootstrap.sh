#!/bin/sh
#
# Bootstrap Drupal
# Author: Kevin Wenger
#
# Run as `./bootstrap.sh`
#

scriptDir=$( cd "$(dirname "${BASH_SOURCE}")" ; pwd -P )

UUID="e85e1685-b207-4ca4-987d-43b3619f58ab"

composer install

printf "\e[1;35m********************************\e[0m\n"
printf "\e[1;35m* Install Drupal from scratch. *\e[0m\n"
printf "\e[1;35m********************************\e[0m\n"

cd "web"

../vendor/bin/drush si standard --account-name=admin --account-pass=admin --account-mail=dev@antistatique.net -y
../vendor/bin/drush ev '\Drupal::entityManager()->getStorage("shortcut_set")->load("default")->delete();'
../vendor/bin/drush config-set system.site uuid "${UUID}" -y

# Sometimes Drupal 8.4.x import configs in wrong orders.
# So we repeat the config-import max. 3 times on successive fails.
n=0
until [ $n -ge 3 ]; do
  ../vendor/bin/drush config-import -y
  n=$((n + 1))
  # delay 3s.
  sleep 3
done

../vendor/bin/drush updatedb -y
