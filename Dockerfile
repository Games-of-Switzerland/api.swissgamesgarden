FROM antistatique/php-dev:7.3-node10

WORKDIR /var/www

# Install additionnal dependencies.
RUN apt-get update && apt-get -y install \
    cron;

# Install additionnal PHP extensions.
RUN docker-php-ext-install \
    exif;

# Copy cron file to the cron.d directory with proper execution rights on the cron job.
ADD ./docker/cron /etc/cron.d/cron

# Add the composer files to install dependencies before copying (optimization).
ADD ./composer.json ./composer.lock ./
RUN set -eux; \
  \
  composer install --prefer-dist --no-scripts --no-progress --no-suggest --no-interaction; \
  composer clear-cache

# Copy everything excepted things excluded from .dockerignore.
COPY . ./

# Ensure patch from Composer will be applied.
RUN set -eux; \
  \
  jq 'del(.. |."patches_applied"? | select(. != null))' ./vendor/composer/installed.json > ./vendor/composer/installed.json.new; \
  mv ./vendor/composer/installed.json.new ./vendor/composer/installed.json; \
  \
  composer install --prefer-dist --no-progress --no-suggest --no-interaction; \
  composer clear-cache;
