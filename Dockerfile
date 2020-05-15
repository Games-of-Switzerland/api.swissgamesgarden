FROM antistatique/php-dev:7.3-node10

WORKDIR /var/www

ADD ./composer.json ./composer.lock ./
RUN set -eux; \
  \
  composer install --prefer-dist --no-autoloader --no-scripts --no-progress --no-suggest --no-interaction; \
  composer clear-cache

COPY . ./

RUN set -eux; \
  \
  mkdir -p .codeship/build; \
  \
  jq 'del(.. |."patches_applied"? | select(. != null))' ./vendor/composer/installed.json > ./vendor/composer/installed.json.new; \
  mv ./vendor/composer/installed.json.new ./vendor/composer/installed.json; \
  \
  composer install --prefer-dist --no-progress --no-suggest --no-interaction; \
  composer clear-cache;
