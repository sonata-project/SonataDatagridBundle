# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

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
