# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
- adds ```file``` validation rule to check for the existence of a file
- adds ```directory``` validation rule to check for the existence of a directory
- adds ```Ares\Rule\AbstractRule``` as validation rule base class
- improves validation schema parsing to detect inapplicable validation rule usage
- adds ```tuple``` type to enable validation of fixed length heterogeneous array structures

## [v1.0.0] 2019-04-14
### Added
- adds ```Ares\RuleFactory``` to handle validation rule registration and instantiation
- extends ```Ares\Validator::__construct()``` with ```$ruleFactory``` parameter
- adds documentation for custom validation rules to ```README.md```
- adds table of contents to ```README.md```
- adds support for custom validation messages
### Changed
- Refactors internal schema processing from arrays to use interal model clases
### Removed
- removes ```$errorMessageRenderer``` parameter from ```Ares\Validator::__construct()```

## [v0.2.0] 2019-03-31
### Added
- ```email``` validation rule (string)
- ```url``` validation rule (string)
- ```min``` validation rule (float, integer)
- ```max``` validation rule (float, integer)
- ```regex``` validation rule (string)
- ```list``` type

## [v0.1.0] 2019-03-28
### Added
- ```CHANGELOG.md``` to track changes between releases of the library
- ```README.md``` to give initial guidance on how to use the library

