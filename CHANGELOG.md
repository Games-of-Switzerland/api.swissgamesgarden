# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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

[Unreleased]: https://github.com/Games-of-Switzerland/gos-server/compare/0.2.0...HEAD
[0.2.0]: https://github.com/Games-of-Switzerland/gos-server/compare/0.1.0...0.2.0
[0.1.0]: https://github.com/Games-of-Switzerland/gos-server/compare/0.0.3...0.1.0
[0.0.3]: https://github.com/Games-of-Switzerland/gos-server/compare/0.0.2...0.0.3
[0.0.2]: https://github.com/Games-of-Switzerland/gos-server/compare/0.0.1...0.0.2
[0.0.1]: https://github.com/Games-of-Switzerland/gos-server/releases/tags/0.0.1
