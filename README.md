#  🎮👾 Swiss Games Garden

Swiss Games Garden API project is based on 💦 [Drupal](https://drupal.org/), 🕸 [Json:API](https://jsonapi.org/) and 🥃 [Gin](https://github.com/EasyCorp/EasyAdminBundle) as Admin UI.
We built it around 🔍 [Elasticsearch](https://www.elastic.co/) to expose Search Engine capabilities.
It uses 🐳 [Docker](http://docker.com/) for running.
We use 📝 [Swagger](https://swagger.io/) for documentation and ✅ [PHPUnit](https://phpunit.de/)/[Behat](https://docs.behat.org) for testing.
We deploy with 🚀 [Capistrano](https://github.com/capistrano/capistrano) and mange our dependencies with 🎶 [Composer](https://getcomposer.org/) & 🏜 [Phive](https://phar.io/).

We made it with 💗.


| Build Status | Swagger | Issues | Activity |
|:-------------------:|:----------------:|:----------------:|:----------------:|
| [![Continuous Integration & Continuous Deployment](https://github.com/Games-of-Switzerland/api.swissgamesgarden/actions/workflows/ci-cd.yml/badge.svg)](https://github.com/Games-of-Switzerland/api.swissgamesgarden/actions/workflows/ci-cd.yml) | [![Swagger](https://img.shields.io/badge/documentation-swagger-blue)](https://api.swissgames.garden/swagger) | ![GitHub issues](https://img.shields.io/github/issues/Games-of-Switzerland/swissgamesgarden?style=flat-square) | ![GitHub last commit](https://img.shields.io/github/last-commit/Games-of-Switzerland/swissgamesgarden?style=flat-square) |

## 🔧 Prerequisites

First of all, you need to have the following tools installed globally on your environment:

* docker
* composer
* drush
* phive

don't forget to add bins to your path such:

* php
* mysql

## 🐳 Docker Install

### Project setup

```bash
cp docker-compose.override-example.yml docker-compose.override.yml
```

Update any values as needed, example when you already use the 8080 port:

```yml
services:
  # Drupal development server
  dev:
    hostname: dev
    ports:
      - "8082:80"
```

Another example when you already have a local MySQL server using port 3306:

```yml
# Database
db:
  ports:
    - "13306:3306"
```

### Project bootstrap

    docker-compose build --pull
    docker-compose up --build -d
    docker-compose exec app docker-as-drupal bootstrap --with-default-content --with-elasticsearch
    (get a coffee, this will take some time...)
    docker-compose exec app drush eshs
    docker-compose exec app drush eshr
    docker-compose exec app drush queue-run elasticsearch_helper_indexing

### Project setup

Once the project up and running via Docker, you may need to setup some configurations in the `web/sites/default/setting.php`.

#### Project base URL

As we are working in a decoupled architecture, we need to set the Website URL.

```php
/**
 * Base URL of the Next App.
 *
 * This value should not contain a leading slash (/).
 *
 * @var string
 */
$config['frontend']['base_url'] = 'https://gos.museebolo.ch';
```

#### Sitemap

The base URL of sitemap links can be overridden using the following settings.

```php
/**
 * The base URL of sitemap links can be overridden here.
 *
 * @var string
 */
$config['simple_sitemap.settings']['base_url'] = 'https://api-gos.museebolo.ch';
```

#### Symfony Mailer, Sendmail & Mailcatcher

We use Symfony Mailer to manager the Mail Transport.
For this project, a container `Sendmail` provide us a SMTP server that is only usable by docker engine.
The sendmail server should only be used to properly send mail.

```php
/**
 * The Symfony Mailer transporter.
 *
 * @var string
 */
$config['symfony_mailer.settings']['default_transport'] = 'smtp';
$config['symfony_mailer.mailer_transport.smtp']['configuration']['host'] = 'sendmail';
$config['symfony_mailer.mailer_transport.smtp']['configuration']['port'] = '25';
```

For local development, we use `mailcatcher` as a fake SMTP server.
Mailcatcher will prevent mail to be sent and expose them through a Web UI on [http://localhost:1080](http://localhost:1080).

```php
/**
 * The Symfony Mailer transporter.
 *
 * @var string
 */
$config['symfony_mailer.settings']['default_transport'] = 'smtp';
$config['symfony_mailer.mailer_transport.smtp']['configuration']['host'] = 'mailcatcher';
$config['symfony_mailer.mailer_transport.smtp']['configuration']['port'] = '1025';
```

#### CND

We use an "Origin Pull CDNs" via `https://api.swissgames.garden`. This CDN will be used for every static-content excepted js & css.
Obviously, you need to override this URL or disable the CDN for you local env.

```php
/**
 * The CDN static-content status.
 *
 * @var boolean
 */
$config['cdn.settings']['status'] = false;
```

By default, we decide to disable the CDN for development process, as the host port may vary by developers and therefore
the `mapping.domain` may change.

```php
/**
 * The CDN mapping domain to be used for static-content.
 *
 * @var string
 */
$config['cdn.settings']['mapping']['domain'] = 'api.swissgames.garden';
```

#### Elasticsearch prefix

We use only 1 Elasticsearch server for both Production & Staging environments. Doing so, we need to separate our indexes
by name. We decide to use prefixes to achieve this goal.

```php
/**
 * Setting used to add a prefix for ES index based on the environment.
 */
$settings['gos_elasticsearch.index_prefix'] = 'local';
```

### When it's not the first time

    docker-compose build --pull
    docker-compose up --build -d
    docker-compose exec app drush cr (or any other drush command you need)
    docker-compose exec app docker-as-drupal db-reset --with-default-content
    docker-compose exec app drush eshr
    docker-compose exec app drush queue-run elasticsearch_helper_indexing

### (optional) Get the productions images

    bundle exec cap production files:download

### Docker help

    docker-compose exec app docker-as-drupal --help

## 🚔 Static Analyzers

All Analyzers are installed using PHive. Some extra analyzer dependencies are installed using Composer.

## Drupal coding standards & Drupal best practices

You need to run composer before using PHPCS. The Drupal and DrupalPractice Standard will automatically be applied following the rules on `phpcs.xml.dist` file

### Command Line Usage

Check Drupal coding standards & Drupal best practices:

```bash
./vendor/bin/phpcs
```

Automatically fix coding standards

```bash
./vendor/bin/phpcbf
```

### Improve global code quality using PHPCPD (Code duplication) &  PHPMD (PHP Mess Detector).

Detect overcomplicated expressions & Unused parameters, methods, properties

```bash
./tools/phpmd ./web/modules/custom text ./phpmd.xml \
--suffixes php,module,inc,install,test,profile,theme,css,info,txt --exclude *Test.php

./tools/phpmd ./behat text ./phpmd.xml --suffixes php
```

Copy/Paste Detector

```bash
./tools/phpcpd ./web/modules/custom --suffix .php --suffix .module --suffix .inc --suffix .install --suffix .test \
--suffix .profile --suffix .theme --suffix .css --suffix .info --suffix .txt --exclude tests

./tools/phpcpd ./behat
```

### Ensure PHP Community Best Practicies using PHP Coding Standards Fixer

It can modernize your code (like converting the pow function to the ** operator on PHP 5.6) and (micro) optimize it.

We must add one extra dependencies (via Composer) to work properly with Drupal:
- `drupol/phpcsfixer-configs-drupal`

```bash
./vendor/bin/php-cs-fixer fix --dry-run --format=checkstyle
```

### Attempts to dig into your program and find as many type-related bugs as possiblevia Psalm

We must add one extra dependencies (via Composer) to work properly with Drupal:
- `fenetikm/autoload-drupal`

```bash
./tools/psalm
```

### Catches whole classes of bugs even before you write tests using PHPStan

We must add two extra dependencies (via Composer) to work properly with Drupal:
- `mglaman/phpstan-drupal`
- `phpstan/phpstan-deprecation-rules`

```bash
./tools/phpstan analyse ./web/modules/custom ./behat ./web/themes --error-format=checkstyle
```

### Enforce code standards with git hooks

Maintaining code quality by adding the custom post-commit hook to yours.

```bash
cat ./scripts/hooks/post-commit >> ./.git/hooks/post-commit
```

## After a git pull/merge

```bash
docker-compose down
docker-compose build --pull
docker-compose up --build -d
docker-compose exec app docker-as-drupal db-reset --with-default-content --with-elasticsearch
```

Prepend every command with `docker-compose exec app` to run them on the Docker
environment.

## 🚀 Deploy

### First time

```bash
# You need to have ruby & bundler installed
$ bundle install
```

### Each times

We use Capistrano to deploy:

```bash
bundle exec cap -T
bundle exec cap staging deploy
```

## 🔍 Elasticsearch

All given port may be changed by your own `docker-compose.override.yml`.

The Docker installation ship with a working **Elasticsearch in version 6.8.5**.

You may browse your ES server by using [DejaVu UI](https://github.com/appbaseio/dejaVu).

1. Open **DejaVu** in your local browser `http://localhost:1358/`

2. Connect to your Elasticsearch instance using `http://localhost:19200` on index `real_estate`.

    Example working link:
    [http://localhost:1358/?appname=development_gos_node_game_en&url=http://localhost:19200](http://localhost:1358/?appname=development_gos_node_game_en&url=http://localhost:19200)

    The local machine port is the one defined in your `docker-compose` or `docker-compose.override.yml`.
    In the following example the local port is `19200`. and the port inside the Docker is `9200`.

    ```yaml
      elasticsearch:
        ports:
          - "19200:9200"
    ```

### Index

```bash
docker-compose exec [app|test] drush eshr
docker-compose exec [app|test] drush queue-run elasticsearch_helper_indexing
```

### List of Indexes

```bash
docker-compose exec elasticsearch curl http://127.0.0.1:9200/_cat/indices
```

This should print

```bash
$ yellow open gos lsSuUuMjTyizjL_WLECfyQ 5 1 0 0 1.2kb 1.2kb
```

### Recreate Index from scratch

This operation is necessary when the Elasticsearch schema has been updated.

```bash
    docker-compose exec app drush eshd -y
    docker-compose exec app drush eshs
```

### Health Check

Check that Elasticsearch is up and running.

```bash
docker-compose exec elasticsearch curl http://127.0.0.1:9200/_cat/health
```

## 📋 Documentations

We use *Swagger* to document our custom REST endpoints.

Expects the `swagger.json` file it to be stored ìn `./swagger/swagger.json`.
You may access to the *staging* or *production* REST specification with those links:

- Production: [api.swissgames.garden/swagger](https://api.swissgames.garden/swagger)
- Staging: [staging-api.swissgames.garden/swagger](https://staging-api.swissgames.garden/swagger)

Customs modules:

 - [Migration](./web/modules/custom/gos_migrate/README.md)

## 🚑 Troubleshootings

### Error while running Elasticsearch Setup ?

```
  No alive nodes found in your cluster
```

It seems your Elasticsearch cluster is not reachable by the Docker container.

The common mistake is a misconfiguration on `docker-compose.yml` with missing `host`:

```
DRUPAL_CONFIG_SET: >-
    elasticsearch_helper.settings elasticsearch_helper.host elasticsearch
```

Run the diagnostic command to show the value of `elasticsearch_helper.host` on your container:

```
docker-compose exec app drush cget elasticsearch_helper.settings --include-overridden
```

It should print:

```
elasticsearch_helper:
  scheme: http
  host: elasticsearch
  port: 9200
  authentication: 0
  user: ''
  password: ''
  defer_indexing: 0
```

If you get something else in `host` (such as `localhost`), then your initial bootstrap was made without the `host` config key and need to be rerun:

```
docker-compose exec app docker-as-drupal db-reset --update-dump --with-default-content
```

### Elasticsearch indexing failed with error `FORBIDDEN/12/index read-only / allow delete` ?

By default, Elasticsearch installed goes into `read-only mode when you have less than 5% of free disk space.

First, you will need to remove all documents and indices from Elasticsearch (or change the disk size).

```bash
docker-compose exec elasticsearch curl -X DELETE http://127.0.0.1:9200/_all
```

Then you can fix it by running the following commands:

```bash
docker-compose exec elasticsearch curl -XPUT -H "Content-Type: application/json" http://127.0.0.1:9200/_cluster/settings -d '{ "transient": { "cluster.routing.allocation.disk.threshold_enabled": false } }'
docker-compose exec elasticsearch curl -XPUT -H "Content-Type: application/json" http://127.0.0.1:9200/_all/_settings -d '{"index.blocks.read_only_allow_delete": null}'
docker-compose exec elasticsearch curl -XPUT -H "Content-Type: application/json" http://127.0.0.1:9200/_cluster/settings -d '{ "transient": { "cluster.routing.allocation.disk.threshold_enabled": false } }'
```

### Error while importing config ?

```
The import failed due for the following reasons:                                                                                                   [error]
Entities exist of type <em class="placeholder">Shortcut link</em> and <em class="placeholder"></em> <em class="placeholder">Default</em>. These
entities need to be deleted before importing.
```

Solution 1: Delete all your shortcuts from the Drupal Admin on [admin/config/user-interface/shortcut/manage/default/customize](admin/config/user-interface/shortcut/manage/default/customize).

Solution 2: Delete all your shortcuts with drush

```bash
drush ev '\Drupal::entityManager()->getStorage("shortcut_set")->load("default")->delete();'
```

### How to disable the Drupal Cache for dev ?

The tricks is to add this two lines in your `settings.php`:

```php
// do this only after you have installed the drupal
$settings['container_yamls'][] = DRUPAL_ROOT . '/sites/development.services.yml';
$settings['cache']['bins']['render'] = 'cache.backend.null';
```

A better way is to use the `example.settings.local.php` that do more for your dev environement (think about it like the `app_dev.php` front controller):

1. Copy the example local file:

    ```bash
    cp sites/example.settings.local.php sites/default/settings.local.php
    ```

2. Uncomment the following line in your `settings.php`

    ```php
    if (file_exists(__DIR__ . '/settings.local.php')) {
      include __DIR__ . '/settings.local.php';
    }
    ```

3. Clear the cache

    ```bash
    drush cr
    ```

### How to enable the Twig Debug for dev ?

1. Copy the example local file:

    ```bash
    cp sites/default/default.services.yml sites/default/services.yml
    ```

2. set the debug value of twig to `true`

    ```php
    twig.config:
      debug: true
    ```

3. Clear the cache

    ```bash
    drush cr
    ```

[Read More about it](https://www.drupal.org/node/1903374)

### Trouble when running coding standard validations

```bash
ERROR: the "Drupal" coding standard is not installed. The installed coding standards are MySource, PEAR, PHPCS, PSR1, PSR2, Squiz and Zend
```

You have to register the Drupal and DrupalPractice Standard with PHPCS:

```bash
./vendor/bin/phpcs --config-set installed_paths [absolute-path-to-vendor]/drupal/coder/coder_sniffer
```

## 🏆 Tests

Every tests should be run into the Docker environment.

1. Run a shell on your Docker test env.

```bash
docker-compose exec test bash
```

1. Once connected via ssh on your Docker test, you may run any `docker-as-drupal` commands

```bash
docker-as-drupal [behat|phpunit|nightwatch]
```

You also may use the direct access - without opening a bash on the Docket test env. using:

```bash
docker-compose exec test docker-as-drupal [behat|phpunit|nightwatch]
```

## 💻 Drush Commands

## 🕙 Crons

```
# Drupal - Production
# ----------------
## Every 5 minutes
*/5 * * * * root /var/www/docker/cron.sh 2>&1
```

### Crontab

## 📢 RSS

## 📈 Monitoring

### New Relic

New Relic requires two components to work: the PHP agent (inside our `app` container) and a daemon (another container), which aggregates data sent from one or more agents and sends it to New Relic.

By default, we removed the New Relic Docker Container `ARGS` and `depends_on` to avoid building extra containers for developers.
Therefore, on Staging & Production `docker-compose.override.yml` we have added thoses extra parameters

```yaml
build:
  context: .
  args:
    - 'NEW_RELIC_AGENT_VERSION=9.18.1.303'
    - 'NEW_RELIC_LICENSE_KEY=LICENSE'
    - 'NEW_RELIC_APPNAME=Games of Switzerland'
    - 'NEW_RELIC_DAEMON_ADDRESS=newrelic-apm-daemon:31339'
depends_on:
    - newrelic-apm-daemon
```

You also may add the API Key in `settings.php` (on staging / production) to enable data-collection of contrib module `new_relic_rpm

```php
$config['new_relic_rpm.settings']['api_key'] = 'YOUR_API_KEY';
```

## Authors

👨‍💻 **Kevin Wenger**

* Twitter: [@wengerk](https://twitter.com/wengerk)
* Github: [@wengerk](https://github.com/wengerk)

👨‍💻 **Toni Fisler**

* Twitter: [@tonifisler](https://twitter.com/tonifisler)
* Github: [@tonifisler](https://github.com/tonifisler)

👩‍💻 **Camille Létang**

* Github: [@CamilleLetang](https://github.com/CamilleLetang)

👨‍💻 **Pierre Georges**

* Github: [@pierre-georges](https://github.com/pierre-georges)

## 🤝 Contributing

Contributions, issues and feature requests are welcome!

Feel free to check [issues page](https://github.com/Games-of-Switzerland/swissgamesgarden/issues).
