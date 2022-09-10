# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
- allow link to be button - Games-of-Switzerland/swissgamesgarden#9

### Changed
- rename Game Json:api field_cantons to cantons- Games-of-Switzerland/swissgamesgarden#51

## [0.5.0] - 2022-09-09
### Added
- configure Gandi to send e-mails - Games-of-Switzerland/swissgamesgarden#91
- add the role 'Contributor' - Games-of-Switzerland/swissgamesgarden#101

### Fixed
- ensure master is deployed on production instead of dev

### Security
- update Drupal 9.3.7 => 9.4.3 with all dependencies
- upgrade module drupal/bamboo_twig (5.0.0-alpha1 => 5.0.0)
- upgrade theme drupal/gin (3.0.0-beta1 => 3.0.0-beta5)
- update module drupal/gin_toolbar (1.0.0-beta21 => 1.0.0-beta22)
- update module drupal/gin_login (1.1.0 => 1.3.0)
- update module drupal/ctools (3.7.0 => 4.0.1)
- update module drupal/admin_toolbar (3.1.0 => 3.1.1)
- update module drupal/pathauto (1.9.0 => 1.11.0)
- update module drupal/token (1.10.0 => 1.11.0)
- update module drupal/metatag (1.19.0 => 1.21.0)
- update module drupal/image_effects (3.2.0 => 3.3.0)
- update module drupal/consumer_image_styles (4.0.6 => 4.0.7)
- update module drupal/consumers (1.12.0 => 1.13.0)
- update module drupal/jsonapi_extras (3.20.0 => 3.21.0)
- update module drupal/restui (1.20.0 => 1.21.0)
- update module drupal/symfony_mailer (1.0.0-alpha4 => 1.0.0-alpha11)
- update all other dependencies (16 updates)
- update linter phar phpmd 2.9.1 => 2.12.0
- update linter phar psalm 4.13.0 => 4.27.0
- update linter phar phpstan 1.2.0 => 1.8.5

## [0.4.0] - 2022-09-08
### Changed
- use Docker Builtkit on Github Action
- rework Docker integration using antistatique/php instead of deprecated image antistatique/docker-php

## [0.3.1] - 2022-05-05
### Security
- update module drupal/pathauto (1.9.0 => 1.10.0)
- update module drupal/migrate_plus (5.2.0 => 5.3.0)
- update module drupal/drupal-driver (v2.1.1 => v2.1.2)
- update module drupal/jsonapi_hypermedia (1.6.0 => 1.7.0)
- update module drupal/drupal-extension (v4.1.0 => v4.2.1)

## [0.3.0] - 2022-04-22
### Security
- update Drupal 9.3.7 => 9.3.12 with all dependencies
- update module drupal/gin (3.0.0-beta1 => 3.0.0-beta2)
- update module drupal/gin_login (1.1.0 => 1.2.0)
- update module drupal/gin_toolbar (1.0.0-beta21 => 1.0.0-beta22)
- update module drupal/migrate_file (2.1.0 => 2.1.1)
- update module drupal/consumer_image_styles (4.0.6 => 4.0.7)

## [0.2.0] - 2022-03-24
### Security
- update Drupal 9.2.8 => 9.3.7 with all dependencies

### Added
- add a migration update to fillup Website field on Games
- add facets extra data for platform name, location name & genre name - Games-of-Switzerland/swissgamesgarden#50
- add support of Games canton(s) - Games-of-Switzerland/swissgamesgarden#51

## [0.1.0] - 2022-03-14
### Added
- add the CDN module to serve all images via HTTPs

### Changed
- install some static analyzer via Phive instead of Composer
- update to PHP 8.0
- update New Relic Agent 9.13.0.270 -> 9.18.1.303 for PHP8 compatibility

## [0.0.3] - 2021-11-20
### Added
- update deployment process to use Github Actions Composite
- update deployment process to set Github Environements

### Changed
- bump composer/composer from 2.0.13 to 2.1.12
- upgrade from Composer v1 to v2

## [0.0.2] - 2021-11-15
### Changed
- update to Drupal 9

## [0.0.1] - 2021-11-15
### Added
- init repo on 2019-11-27
- add Basic pages to JSON:API - #140

### Changed
- update changelog following 'keep a changelog'
- add Video Games collection to Gin Login
- prepare for Drupal 9 update

[Unreleased]: https://github.com/Games-of-Switzerland/gos-server/compare/0.5.0...HEAD
[0.5.0]: https://github.com/Games-of-Switzerland/gos-server/compare/0.4.0...0.5.0
[0.4.0]: https://github.com/Games-of-Switzerland/gos-server/compare/0.3.1...0.4.0
[0.3.1]: https://github.com/Games-of-Switzerland/gos-server/compare/0.3.0...0.3.1
[0.3.0]: https://github.com/Games-of-Switzerland/gos-server/compare/0.2.0...0.3.0
[0.2.0]: https://github.com/Games-of-Switzerland/gos-server/compare/0.1.0...0.2.0
[0.1.0]: https://github.com/Games-of-Switzerland/gos-server/compare/0.0.3...0.1.0
[0.0.3]: https://github.com/Games-of-Switzerland/gos-server/compare/0.0.2...0.0.3
[0.0.2]: https://github.com/Games-of-Switzerland/gos-server/compare/0.0.1...0.0.2
[0.0.1]: https://github.com/Games-of-Switzerland/gos-server/releases/tags/0.0.1
