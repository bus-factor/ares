# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [3.0.0] 2020-08-16
### Added
- drops PHP < 7.1 support
- adds ext-json dependency (PHP extension)
### Fixed
- validations for nullable fields are now skipped if null value provided

## [2.1.0] 2019-11-24
### Added
- adds ```uuid``` validation rule

## [2.0.1] 2019-04-28
### Fixed
- fixed issue when custom type schema contained multiple references to itself

## [2.0.0] 2019-04-28
### Added
- ```length```, ```maxlength```, ```minlength``` are now applicable to ```list``` types
- adds ```unknownAllowed``` validation rule
- adds sanitization feature
- introduces ```Ares\Ares``` as facade for data validation and sanitization
- adds custom types to allow for easier schema reuse
- adds ```Ares\Validation\RuleRegistry``` to replace ```Ares\Validation\RuleFactory```
### Changed
- ```'required' => true``` is now validation default behavior 
- renames validation option ```allowUnknown``` to ```allUnknownAllowed```
- moves all validation related classes into sub-namespace ```Validation```
### Removed
- removed ```Ares\Validation\RuleFactory```

## [v1.1.1] 2019-04-20
### Added
- fixes issue that type ```tuple``` accepted more items than defined in the schema without validation errors

## [v1.1.0] 2019-04-19
### Added
- adds ```file``` validation rule to check for the existence of a file
- adds ```directory``` validation rule to check for the existence of a directory
- adds ```Ares\Rule\AbstractRule``` as validation rule base class
- improves validation schema parsing to detect inapplicable validation rule usage
- adds ```tuple``` type to enable validation of fixed length heterogeneous array structures
- adds ```numeric``` type to enable validation of numeric values (integer or float)
- adds ```length``` validation rule to check the exact length of a string

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

