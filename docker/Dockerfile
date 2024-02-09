# syntax=docker/dockerfile:1.3

FROM antistatique/php:8.1-drupal

ARG user=www-data
ARG group=${user}

# Export New Relic as ENV variables
ARG NEW_RELIC_AGENT_VERSION
ARG NEW_RELIC_LICENSE_KEY
ARG NEW_RELIC_APPNAME
ARG NEW_RELIC_DAEMON_ADDRESS

# Add and Run the newrelic agent installer.
ADD --chown=${user}:${group} ./docker/newrelic.sh ./docker/newrelic.sh
RUN ./docker/newrelic.sh

# Feel free to disable those lines to use Xdebug on your side.
#RUN mkdir -p /usr/src/php/ext/xdebug && \
#    pecl install xdebug && \
#    docker-php-ext-enable xdebug \
#    && echo "xdebug.mode=develop,debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
#    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
#    && echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
#    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# This part is used to set the correct group & user to the files copied from host -> container
# Somehow due to MacOS host to Linux container, the files are not properly chowned or the container doesn't run
# Properly with the correct user. This fixes the issue.
# -----------------------------------------------------------

# Install additional dependencies.
RUN apt-get update && apt-get -y install \
    cron \
    gnupg;

# Install additional PHP extensions.
RUN docker-php-ext-install \
    exif;

# This part is used to set the correct group & user to the files copied from host -> container
# Somehow due to MacOS host to Linux container, the files are not properly chowned or the container doesn't run
# Properly with the correct user. This fixes the issue.
# -----------------------------------------------------------
RUN set -eux; \
  \
  if [ "${group}" != "www-data" ]; then \
    groupadd user --gid ${group}; \
  fi; \
  \
  if [ "${user}" != "www-data" ]; then \
    useradd user --uid ${user} --gid ${group} --groups www-data --no-create-home --shell /bin/bash; \
  fi; \
  \
  chown -R $user:$group . /var/backups

# Copy cron file to the cron.d directory with proper execution rights on the cron job.
ADD --chown=${user}:${group} ./docker/cron /etc/cron.d/cron

## Allowing to run cron with non-root user (www-data).
RUN chmod gu+rw /var/run
RUN chmod gu+s /usr/sbin/cron
#RUN touch /var/run/crond.pid; \
#    chgrp www-data /var/run/crond.pid; \
#    chmod 764 /var/run/crond.pid
# usermod -a -G root,crontab www-data

# The goal of this is to not run anymore the command as root.
# But also for Windows Hosts to allow same user in Docker as the host.
USER $user:$group
# -----------------------------------------------------------

ENV COMPOSER_DISCARD_CHANGES=true

# Add the composer files to install dependencies before copying (optimization).
ADD --chown=${user}:${group} ./composer.json ./composer.lock ./
COPY --chown=${user}:${group} ./patches ./patches
RUN set -eux; \
  \
  composer install --prefer-dist --no-scripts --no-progress --no-interaction; \
  composer clear-cache

# Ensure patch from Composer will be applied.
RUN set -eux; \
  \
  jq 'del(.. |."patches_applied"? | select(. != null))' ./vendor/composer/installed.json > ./vendor/composer/installed.json.new; \
  mv ./vendor/composer/installed.json.new ./vendor/composer/installed.json; \
  \
  composer install --prefer-dist --no-progress --no-interaction; \
  composer clear-cache;

# Copy everything excepted things excluded from .dockerignore.
COPY --chown=${user}:${group} . ./
