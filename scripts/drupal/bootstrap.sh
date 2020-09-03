#!/bin/sh
#
# Bootstrap Drupal
# Author: Kevin Wenger
#
# Run as `./bootstrap.sh`
#

scriptDir=$( cd "$(dirname "${BASH_SOURCE}")" ; pwd -P )

DEFAULT_CONTENT="gos_default_content"
UUID="e85e1685-b207-4ca4-987d-43b3619f58ab"

SKIP_DEPEDENCIES=0
SKIP_INTERACTION=0
SKIP_DEFAULT=0
SAVE_DATABASE=0
DATABASE_URL=""
MAILCATCHER=""
PRIVATE_FILES=""

while [ $# -gt 0 ]; do
  case "$1" in
    --skip-dependencies=*)
      SKIP_DEPEDENCIES=1
      ;;
    --skip-default=*)
      SKIP_DEFAULT=1
      ;;
    --save-clean-database=*)
      SAVE_DATABASE=1
      ;;
    --database=*)
      DATABASE_URL="${1#*=}"
      ;;
    --mailcatcher=*)
      MAILCATCHER="${1#*=}"
      ;;
    --skip-interaction=*)
      SKIP_INTERACTION="-y"
      ;;
    --private-files=*)
      PRIVATE_FILES="${1#*=}"
      ;;
    *)
      printf "\e[1;91m***************************\e[0m\n"
      printf "\e[1;91m* Error: Invalid argument.*\e[0m\n"
      printf "\e[1;91m***************************\e[0m\n"
      echo "script usage: $(basename $0) [--skip-dependencies=1] [--skip-default=1] [--database=DATABASE_URL] [--private-files=\"./path/to/private\"] [--skip-interaction=1]" >&2
      exit 1
  esac
  shift
done

if [ $SKIP_DEPEDENCIES -eq 0 ]
then
  printf "\e[1;34m*************************\e[0m\n"
  printf "\e[1;34m* Install dependencies. *\e[0m\n"
  printf "\e[1;34m*************************\e[0m\n"

  yarn install
  composer install
  bundle install
  bundle exec cap staging styleguide:build_local
fi

if [ ! -z "$MYSQL_USER" ]
then
  DATABASE_URL="mysql://$MYSQL_USER:$MYSQL_PASSWORD@localhost/development$TEST_ENV_NUMBER"
fi

printf "\e[1;35m********************************\e[0m\n"
printf "\e[1;35m* Install Drupal from scratch. *\e[0m\n"
printf "\e[1;35m********************************\e[0m\n"

printf "\e[1;93mthe database is: $DATABASE_URL\e[0m\n"

cd "web"

../vendor/bin/drush si standard --db-url=$DATABASE_URL --site-name='Tests' --account-name=admin --account-pass=admin --account-mail=dev@antistatique.net $SKIP_INTERACTION
../vendor/bin/drush ev '\Drupal::entityManager()->getStorage("shortcut_set")->load("default")->delete();'
../vendor/bin/drush config-set system.site uuid "${UUID}" -y

# Sometimes Drupal 8.4.x import configs in wrong orders.
# So we repeat the config-import max. 3 times on successives fails.
n=0
until [ $n -ge 3 ]; do
  ../vendor/bin/drush config-import -y --source='../config/d8/sync/'
  n=$((n + 1))
  # delay 3s.
  sleep 3
done

../vendor/bin/drush updatedb -y

# Set the Privates Files settings which need to be in the settings.php file
if [ -f ./sites/default/settings.php ]
then
  printf "\e[1;34m*************************\e[0m\n"
  printf "\e[1;34m* Override Settings.php *\e[0m\n"
  printf "\e[1;34m*************************\e[0m\n"
  chmod 775 ./sites/default
  chmod 664 ./sites/default/settings.php

  if [ ! -z "$PRIVATE_FILES" ] && [ -d "$PRIVATE_FILES" ]
  then
    mkdir -p "$PRIVATE_FILES"

    printf "\e[1;93m* \$settings['file_private_path'] = '$PRIVATE_FILES'; *\e[0m\n"
    sed -ri -e "s@.+\\\$settings\['file_private_path'\].+;@\\\$settings\['file_private_path'\] = '$PRIVATE_FILES';@g" ./sites/default/settings.php
  fi

  ../vendor/bin/drush cr
fi

if [ ! -z "$MAILCATCHER" ]
then
  printf "\e[1;35m**********************\e[0m\n"
  printf "\e[1;35m* Setup Mailcatcher. *\e[0m\n"
  printf "\e[1;35m**********************\e[0m\n"

  ./vendor/bin/drush cset swiftmailer.transport transport "smtp" -y
  ../vendor/bin/drush cset swiftmailer.transport smtp_host "${MAILCATCHER%:*}" -y
  ../vendor/bin/drush cset swiftmailer.transport smtp_port "${MAILCATCHER#*:}" -y
  ../vendor/bin/drush cset swiftmailer.transport smtp_encryption '0' -y
fi

if [ $SAVE_DATABASE -eq 1 ]
then
  printf "\e[1;35m******************************************\e[0m\n"
  printf "\e[1;35m* Save database without default content. *\e[0m\n"
  printf "\e[1;35m******************************************\e[0m\n"

  ../vendor/bin/drush sql-dump --result-file=../database-clean.dump.sql -y
fi

if [ $SKIP_DEFAULT -eq 0 ]
then
  printf "\e[1;35m***********************************\e[0m\n"
  printf "\e[1;35m* Run fixtures & default content. *\e[0m\n"
  printf "\e[1;35m***********************************\e[0m\n"

  # Check if the Default Content module exists.
  if ../vendor/bin/drush pm-list | grep -q $DEFAULT_CONTENT
  then
    ../vendor/bin/drush en $DEFAULT_CONTENT -y
    ../vendor/bin/drush pmu hal serialization default_content $DEFAULT_CONTENT -y
  fi
fi
