# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog][keepachangelog] and this project adheres to [Semantic Versioning][semver].

## Unreleased

### Added

- Laravel `11.x` support

### Changed

- Minimal Laravel version now is `10.0`
- Version of `composer` in docker container updated up to `2.7.6`
- Updated dev dependencies

## v2.5.0

### Added

- Support Laravel `10.x`
- Support PHPUnit `v10`

### Changed

- Minimal require PHP version now is `8.0.2`
- Minimal Laravel version now is `9.1.0`
- Composer version up to `2.6.5`
- Package `phpstan/phpstan` up to `^1.10`
- Package `mockery/mockery` up to `^1.6`

### Removed

- Support PHP `7.*` versions

## v2.4.0

### Added

- Support Laravel `9.x`

## v2.3.0

### Removed

- Dependency `tarampampam/wrappers-php` because this package was deprecated and removed

## v2.2.0

### Added

- Support PHP `8.x`

### Changed

- Minimal PHP version now is `7.3`
- Composer `2.x` is supported now

## v2.1.0

### Changed

- Laravel `8.x` is supported now
- Minimal Laravel version now is `6.0` (Laravel `5.5` LTS got last security update August 30th, 2020)
- Dependency `tarampampam/wrappers-php` version `~2.0` is supported

## v2.0.0

### Changed

- Maximal `illuminate/*` packages version now is `7.*`
- Minimal required PHP version now is `7.2`
- Classes `RequestsStack` and `ResponsesStack` do not extend `Illuminate\Support\Collection`
- Interfaces `RequestsStackInterface` and `ResponsesStackInterface` do not extend `Illuminate\Contracts\Support\Arrayable`
- Method `push()` in `RequestsStack` and `ResponsesStack` return `void` now

### Added

- Methods `all()`, `getIterator()`, `count()`, `isEmpty()`, `isNotEmpty()` and `first()` implementation in `RequestsStack` and `ResponsesStack` classes
- Type-hints for methods in `RequestsStackInterface` and `ResponsesStackInterface` interfaces

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
