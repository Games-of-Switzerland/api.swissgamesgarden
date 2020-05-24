# Games of Switzerland - Migration

All migrations are processed using a CSV source located in `web/modules/custom/gos_migrate/source`.

The original source of this CSV file is a Google Spreadsheet accessible there: https://docs.google.com/spreadsheets/d/1pWOGpADxvNEWcYnpTFpCyhS9rtIjCYgIdZmBppDkC2Q/edit#gid=2060099049

## Before run the first full Migrations

Before starting a migration,

### (optional) Clean previous migration

  ```bash
  $ drush pmu gos_migrate -y && drush en gos_migrate -y
  ```

### (optional) Clean the Elasticsearch Index.

  ```bash
  $ drush eshd -y && drush eshs && drush eshr && drush queue-run elasticsearch_helper_indexing && drush cr
  ```

## Migrate

1. Migrate all platforms (Playstation 4, macOS, Xbox, ...)

  ```bash
  $ drush mim gos_platforms
  ```

1. Migrate all studios with appropriates team-member(s)

  ```bash
  $ drush mim gos_studios
  ```

1. Migrate all games that include: platforms (on the-fly-creation) & studios (lookup).

  ```bash
  $ drush mim gos_games
  ```

1. Run a full Elasticsearch indexation process

  ```bash
  $ drush eshr && drush queue-run elasticsearch_helper_indexing && drush cr
  ```

## Debugging

List the migrations with `drush ms`

Limit a migration with `drush mi [migration_name] --limit=2`

Rollback your change with `drush mr [migration_name]`

## Troubleshooting

### Migration stucks

Migration [migration_name] is busy with another operation: Importing [error]

You just need to reset this migration using
  ```bash
  $ drush mrs [migration_name]
  ```