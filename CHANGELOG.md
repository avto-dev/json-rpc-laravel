# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog][keepachangelog] and this project adheres to [Semantic Versioning][semver].

## v1.2.0

### Changed

- CI completely moved from "Travis CI" to "Github Actions" _(travis builds disabled)_
- Minimal required PHP version now is `7.2`
- Minimal required `phpunit/phpunit` version now is `~7.5`
- `phpstan/phpstan` updated up to `^0.12`

### Added

- PHP 7.4 is supported now

## v1.1.0

### Changed

- Removed unnecessary bound checks interfaces in `ServiceProvider.php`. Due to the fact that the service provider of the package is loaded earlier than the service provider of the application

## v1.0.0

### Added

- Basic features wrote

### Changed

- All business logic now concentrated in Kernel class

[keepachangelog]:https://keepachangelog.com/en/1.0.0/
[semver]:https://semver.org/spec/v2.0.0.html
