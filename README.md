# Games of Switzerland 🎮👾

Drupal 8 powered & up and running via Docker.

## 🔧 Prerequisites

First of all, you need to have the following tools installed globally on your environment:

* docker
* composer
* drush

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
    docker-compose exec dev docker-as-drupal bootstrap --with-default-content --with-elasticsearch
    (get a coffee, this will take some time...)
    docker-compose exec dev drush eshs
    docker-compose exec dev drush eshr
    docker-compose exec dev drush queue-run elasticsearch_helper_indexing

### Project setup

Once the project up and running via Docker, you may need to setup some configurations in the `web/sites/default/setting.php`

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
    docker-compose exec dev drush cr (or any other drush command you need)
    docker-compose exec dev docker-as-drupal db-reset --with-default-content
    docker-compose exec dev drush eshr
    docker-compose exec dev drush queue-run elasticsearch_helper_indexing

### (optional) Get the productions images

    bundle exec cap production files:download

### Docker help

    docker-compose exec dev docker-as-drupal --help

## 🚔 Check Drupal coding standards & Drupal best practices

You need to run composer before using PHPCS. Then register the Drupal and DrupalPractice Standard with PHPCS: `./vendor/bin/phpcs --config-set installed_paths "`pwd`/vendor/drupal/coder/coder_sniffer"`

### Command Line Usage

Check Drupal coding standards:

```bash
./vendor/bin/phpcs --standard=Drupal --colors --extensions=php,module,inc,install,test,profile,theme,css,info,txt,md ./web/modules/custom
```

Check Drupal best practices:

```bash
./vendor/bin/phpcs --standard=DrupalPractice --colors --extensions=php,module,inc,install,test,profile,theme,css,info,txt,md ./web/modules/custom
```

Automatically fix coding standards

```bash
./vendor/bin/phpcbf --standard=Drupal --colors --extensions=php,module,inc,install,test,profile,theme,css,info,txt,md ./web/modules/custom
```

Checks compatibility with PHP interpreter versions

```bash
./vendor/bin/phpcf --target 7.2 \
--file-extensions php,module,inc,install,test,profile,theme,info \
./web/modules/custom
```

### Improve global code quality using PHPCPD (Code duplication) &  PHPMD (PHP Mess Detector).

Detect overcomplicated expressions & Unused parameters, methods, properties

```bash
./vendor/bin/phpmd ./web/modules/custom text ./phpmd.xml
```

Copy/Paste Detector

```bash
./vendor/bin/phpcpd ./web/modules/custom
```

### Enforce code standards with git hooks

Maintaining code quality by adding the custom post-commit hook to yours.

```bash
cat ./scripts/hooks/post-commit >> ./.git/hooks/post-commit
```

## After a git pull/merge

```bash
drush cr
drush cim
drush updatedb
drush cr
```

Prepend every command with `docker-compose exec dev` to run them on the Docker
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
docker-compose exec [dev|test] drush eshr
docker-compose exec [dev|test] drush queue-run elasticsearch_helper_indexing
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
    docker-compose exec dev drush eshd -y
    docker-compose exec dev drush eshs
```

### Health Check

Check that Elasticsearch is up and running.

```bash
docker-compose exec elasticsearch curl http://127.0.0.1:9200/_cat/health
```

### List all games

```bash
docker-compose exec elasticsearch curl -X GET "http://127.0.0.1:9200/gos_node_game/_search?pretty"
```

docker-compose exec elasticsearch curl -X GET "http://127.0.0.1:9200/gos_node_game/_search?pretty&explain" -H 'Content-Type: application/json' -d'
{
    "query" : {
        "match_phrase" : {
            "releases.platform": {
                "query": "ps4",
                "analyzer": "search_synonyms"
            }
        }
    }
}
'

docker-compose exec elasticsearch curl -X GET "http://127.0.0.1:9200/gos_node_game/_search?pretty&explain" -H 'Content-Type: application/json' -d'
{
    "query" : {
        "nested": {
            "path" : "releases",
            "query": {
              "query_string": {
                "default_field": "releases.platform",
                "query": "ps4"
              }
            }
        }
    }
}
'

docker-compose exec elasticsearch curl -X GET "http://127.0.0.1:9200/gos_node_game/_search?pretty&explain" -H 'Content-Type: application/json' -d'
{
    "query" : {
      "query_string": {
        "default_field": "title",
        "query": "kill"
      }
    }
}
'

docker-compose exec elasticsearch curl -X GET "http://127.0.0.1:9200/gos_node_game/_search?pretty&explain" -H 'Content-Type: application/json' -d'
{
    "query" : {
      "query_string": {
        "default_field": "desc",
        "query": "memories"
      }
    }
}
'

docker-compose exec elasticsearch curl -X GET "http://127.0.0.1:9200/gos_node_studio/_search?pretty&explain" -H 'Content-Type: application/json' -d'
{
    "query" : {
      "query_string": {
        "default_field": "name",
        "query": "softvar"
      }
    }
}
'

## 📋 Documentations

We use *Swagger* to document our custom REST endpoints.

Expects the `swagger.json` file it to be stored ìn `./swagger/swagger.json`.
You may access to the *staging* or *production* REST specification with those links:

- Production: [api.gos.ch/swagger](https://api.gos.ch/swagger)
- Staging: [staging-api.gos.ch/swagger](https://staging-api.gos.ch/swagger)

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
docker-compose exec dev drush cget elasticsearch_helper.settings --include-overridden
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
docker-compose exec dev docker-as-drupal db-reset --update-dump --with-default-content
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

Setup Drush to run cron every hour.

### Crontab

## 📢 RSS

## Authors

👨‍💻 **Kevin Wenger**

* Twitter: [@wengerk](https://twitter.com/wengerk)
* Github: [@wengerk](https://github.com/wengerk)

👨‍💻 **Toni Fisler**

* Twitter: [@tonifisler](https://twitter.com/tonifisler)
* Github: [@tonifisler](https://github.com/tonifisler)

👩‍💻 **Camille Léthang**

* Github: [@CamilleLetang](https://github.com/CamilleLetang)

👨‍💻 **Pierre Georges**

* Github: [@pierre-georges](https://github.com/pierre-georges)

## 🤝 Contributing

Contributions, issues and feature requests are welcome!

Feel free to check [issues page](https://github.com/Games-of-Switzerland/gos-server/issues).
