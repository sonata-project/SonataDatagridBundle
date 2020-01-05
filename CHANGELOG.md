# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [3.1.1](https://github.com/sonata-project/SonataDatagridBundle/compare/3.1.0...3.1.1) - 2020-01-04
### Fixed
- Fixed compatibility with PHP 7.4
- Fixed default filter type should be FQCN

## [3.1.0](https://github.com/sonata-project/SonataDatagridBundle/compare/3.0.1...3.1.0) - 2020-01-03
### Added
- Add support for symfony 5

## [3.0.1](https://github.com/sonata-project/SonataDatagridBundle/compare/3.0.0...3.0.1) - 2019-10-08
### Fixed
- `TypeError` induced by strict types in `Pager::computeNbResults()`

## [3.0.0](https://github.com/sonata-project/SonataDatagridBundle/compare/2.5.0...3.0.0) - 2019-03-09

### Added
- php 7 parameter and return type hints

### Removed
- support for php 7.1
- possibility for extension in most classes

### Changed
- The `Pager\PagerInterface` implements `\Iterator` and `\Countable`.
- All public methods of `Pager\BasePager` were moved to `Pager\PagerInterface`.


## [2.5.0](https://github.com/sonata-project/SonataDatagridBundle/compare/2.4.0...2.5.0) - 2019-13-15
### Fixed
Fix deprecation for symfony/config 4.2+

### Removed
- support for php 5 and php 7.0

## [2.4.0](https://github.com/sonata-project/SonataDatagridBundle/compare/2.3.1...2.4.0) - 2017-10-09
### Added
- Added `Pager::create` method

### Changed
- All templates references are updated to twig namespaced syntax

### Deprecated
- Deprecate `results.html.twig` template

## [2.3.1](https://github.com/sonata-project/SonataDatagridBundle/compare/2.3.0...2.3.1) - 2017-12-12
### Fixed
- Allow Symfony 4.0

## [2.3.0](https://github.com/sonata-project/SonataDatagridBundle/compare/2.2.1...2.3.0) - 2017-11-20
### Changed
- Removed usage of old form type aliases

### Removed
- Support for old versions of php and Symfony.

## [2.2.1](https://github.com/sonata-project/SonataDatagridBundle/compare/2.2.0...2.2.1) - 2017-02-09
### Fixed
- Doctrine deprecations warnings about `getRootAlias()`
- use `\RuntimeException` instead of `\RunTimeException`
