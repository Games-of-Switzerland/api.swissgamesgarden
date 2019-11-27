FROM antistatique/php-dev:7.2-node10

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
  composer clear-cache; \
  \
  mkdir -p /var/www/vendor/mpdf/mpdf/tmp; \
  chmod 777 /var/www/vendor/mpdf/mpdf/tmp; \
  find "/var/www/vendor/mpdf/mpdf/tmp" -type f -executable -exec chmod -x {} \;; \
  find "/var/www/vendor/mpdf/mpdf/tmp" -type d -exec chmod +xs {} \;
