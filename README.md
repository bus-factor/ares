```
  __ _ _ __ ___  ___
 / _` | '__/ _ \/ __|
| (_| | | |  __/\__ \
 \__,_|_|  \___||___/
```

Ares is a lightweight standalone validation library.

[![Latest Stable Version](https://img.shields.io/packagist/v/bus-factor/ares.svg?style=flat-square)](https://packagist.org/packages/bus-factor/ares)
[![Build Status](https://travis-ci.com/bus-factor/ares.svg?token=6CVThNyY94qpVvuMgX3F&branch=master)](https://travis-ci.com/bus-factor/ares.svg?token=6CVThNyY94qpVvuMgX3F&branch=master)
[![Coverage Status](https://coveralls.io/repos/github/bus-factor/ares/badge.svg?branch=master)](https://coveralls.io/github/bus-factor/ares?branch=master)

# Table of Contents

* [Installation](#installation)
* [Basic Usage](#basic-usage)
* [Validation Errors](#validation-errors)
* [Validation Options](#validation-options)
  * [allBlankable](#validation-options_all-blankable)
  * [allNullable](#validation-options_all-nullable)
  * [allRequired](#validation-options_all-required)
  * [allowUnknown](#validation-options_allow-unknown)
* [Validation Rules](#validation-rules)
  * [allowed](#validation-rules_allowed)
  * [blankable](#validation-rules_blankable)
  * [datetime](#validation-rules_datetime)
  * [directory](#validation-rules_directory)
  * [email](#validation-rules_email)
  * [file](#validation-rules_file)
  * [forbidden](#validation-rules_forbidden)
  * [length](#validation-rules_length)
  * [max](#validation-rules_max)
  * [maxlength](#validation-rules_maxlength)
  * [min](#validation-rules_min)
  * [minlength](#validation-rules_minlength)
  * [nullable](#validation-rules_nullable)
  * [regex](#validation-rules_regex)
  * [required](#validation-rules_required)
  * [schema](#validation-rules_schema)
    * [schema (list)](#validation-rules_schema_schema-list)
    * [schema (map)](#validation-rules_schema_schema-map)
    * [schema (tuple)](#validation-rules_schema_schema-tuple)
  * [type](#validation-rules_type)
  * [url](#validation-rules_url)
* [Custom Validation Messages](#custom-validation-messages)
* [Custom Validation Rules](#custom-validation-rules)

# <a name="installation"></a>Installation

Install the library via composer:
```
composer require bus-factor/ares
```

# <a name="basic-usage"></a>Basic Usage

```php
<?php

use Ares\Validator;

// atomic types
$validator = new Validator(['type' => 'string', 'required' => true]);
$valid = $validator->validate('John Doe');
$errors = $validator->getErrors();

// complex/nested types
$validator = new Validator([
    'type' => 'map',
    'required' => true,
    'schema' => [
        'firstName' => ['type' => 'string', 'required' => true],
        'lastName' => ['type' => 'string', 'required' => true],
    ],
]);
$valid = $validator->validate(['firstName' => 'John', 'lastName' => 'Doe']);
$errors = $validator->getErrors();

```

# <a name="validation-errors"></a>Validation Errors

The ```validate()``` method returns ```true``` if the provided data is valid, otherwise ```false```.

The ```getErrors()``` method returns a list of validation errors that occurred during the last data validation.
The list of validation errors is reset each time ```validate()``` is called.

Each ```Ares\Error``` object implements the ```JsonSerializable``` interface and contains details about the error.

# <a name="validation-options"></a>Validation Options

Validation options may be passed on validator construction:

```php
$schema = [];
$options = [];
$validator = new Validator($schema, $options);
```

Default validation options are:

```php
Validator::OPTIONS_DEFAULTS = [
    'allBlankable' => false,
    'allNullable'  => false,
    'allRequired'  => false,
    'allowUnknown' => false,
]
```

## <a name="validation-options_all-blankable"></a>allBlankable

This option applies to the type ```string``` only.
If set ```true```, blank values are considered valid.
If set ```false```, blank values are considered invalid.

```php
$schema = [
    'type' => 'map',
    'schema' => [
        'name' => ['type' => 'string'],
    ],
];

$validator = new Validator($schema, ['allBlankable' => true]);
$validator->validate(['name' => '']); // -> true

$validator = new Validator($schema, ['allBlankable' => false]);
$validator->validate(['name' => '']); // -> false
```

This option may be overridden per field by using the ```blankable``` rule:
```php
$schema = [
    'type' => 'map',
    'schema' => [
        'name' => ['type' => 'string'],
        'email' => ['type' => 'string', 'blankable' => true],
    ],
];

$validator = new Validator($schema, ['allBlankable' => false]);
$validator->validate(['name' => 'John Doe', 'email' => '']); // -> true
```

## <a name="validation-options_all-nullable"></a>allNullable

If set ```true```, ```null``` is considered a valid value.
If set ```false```, ```null``` is not considered a valid value.

```php
$schema = [
    'type' => 'map',
    'schema' => [
        'name' => ['type' => 'string'],
    ],
];

$validator = new Validator($schema, ['allNullable' => true]);
$validator->validate(['name' => null]); // -> true

$validator = new Validator($schema, ['allNullable' => false]);
$validator->validate(['name' => null]); // -> false
```

This option may be overridden per field by using the ```nullable``` rule:
```php
$schema = [
    'type' => 'map',
    'schema' => [
        'name' => ['type' => 'string'],
        'email' => ['type' => 'string', 'nullable' => true],
    ],
];

$validator = new Validator($schema, ['allNullable' => false]);
$validator->validate(['name' => 'John Doe', 'email' => null]); // -> true
```

## <a name="validation-options_all-required"></a>allRequired

If set ```true``` fields that are defined in the schema and not present in the input, are considered invalid.
If set ```false``` fields that are defined in the schema and not present in the input, are considered valid.

```php
$schema = [
    'type' => 'map',
    'schema' => [
        'name' => ['type' => 'string'],
    ],
];

$validator = new Validator($schema, ['allRequired' => true]);
$validator->validate([]); // -> false

$validator = new Validator($schema, ['allRequired' => false]);
$validator->validate([]); // -> true
```

This option may be overridden per field by using the ```required``` rule:
```php
$schema = [
    'type' => 'map',
    'schema' => [
        'name' => ['type' => 'string'],
        'email' => ['type' => 'string', 'required' => false],
    ],
];

$validator = new Validator($schema, ['allRequired' => true]);
$validator->validate(['name' => 'John Doe']); // -> true
```

## <a name="validation-options_allow-unknown"></a>allowUnknown

This option applies to the type ```map``` only.
If set ```true``` fields that occur in the input data but are not defined in the schema are considered invalid.
If set ```false``` fields that occur in the input data but are not defined in the schema are considered valid.

```php
$schema = [
    'type' => 'map',
    'schema' => [
        'name' => ['type' => 'string'],
    ],
];

$validator = new Validator($schema, ['allowUnknown' => false]);
$validator->validate(['name' => 'John Doe', 'initials' => 'JD']); // -> false

$validator = new Validator($schema, ['allowUnknown' => true]);
$validator->validate(['name' => 'John Doe', 'initials' => 'JD']); // -> true
```

# <a name="validation-rules"></a>Validation Rules

## <a name="validation-rules_allowed"></a>allowed

The ```allowed``` validation rule checks if a value is in a given set of allowed values (enumeration).

Examples:

```php
$validator = new Validator(['type' => 'string', 'allowed' => ['small', 'large']]);
$validator->validate('medium'); // -> false
$validator->validate('small'); // -> true
```

The ```allowed``` validation rule is the opposite of the ```forbidden``` validation rule.

## <a name="validation-rules_blankable"></a>blankable

The ```blankable``` rule applies to ```string``` typed values only.
If set ```true```, blank strings are considered valid.
If set ```false```, blank strings are considered invalid (default).

Examples:

```php
$validator = new Validator(['type' => 'string', 'blankable' => false]);
$validator->validate(''); // -> false
$validator->validate('   '); // -> false
$validator->validate('John Doe'); // -> true

$validator = new Validator(['type' => 'string', 'blankable' => true]);
$validator->validate('   '); // -> true
```

The ```blankable``` validation rule may be used in combination with the ```allBlankable``` validation option.

## <a name="validation-rules_datetime"></a>datetime

The ```datetime``` validation rule applies to ```string``` typed values only.
If set ```true```, any parsable date/time string is considered valid.
If set ```false```, date/time validation will not take place at all.
If set a specific date/time format string, the given value will be checked against that format too.
See [DateTime::createFromFormat()](http://php.net/manual/en/datetime.createfromformat.php) for details about format strings.

Examples:

```php
$validator = new Validator(['type' => 'string', 'datetime' => true]);
$validator->validate('foo'); // -> false
$validator->validate('2018-03-23'); // -> true

$validator = new Validator(['type' => 'string', 'datetime' => 'd.m.Y H:i']);
$validator->validate('2018-03-23'); // -> false
$validator->validate('23.03.2019 00:20'); // -> true
```

## <a name="validation-rules_directory"></a>directory

The ```directory``` validation rule checks if the given string value contains the path to an existing directory.
If set ```true```, only paths to existing directories are considered valid.
If set ```false```, all input is considered valid (no validation).

Examples:

```php
$validator = new Validator(['type' => 'string', 'directory' => true]);
$validator->validate(''); // -> false
$validator->validate(__FILE__); // -> false
$validator->validate(__DIR__); // -> true
```

## <a name="validation-rules_email"></a>email

The ```email``` validation rule checks if a value is a valid email address.
If set ```true```, only valid email addresses are considered valid.
If set ```false```, all input is considered valid (no validation).

Examples:

```php
$validator = new Validator(['type' => 'string', 'email' => true]);
$validator->validate('John Doe'); // -> false
$validator->validate('john.doe@example.com'); // -> true
```

## <a name="validation-rules_file"></a>file

The ```file``` validation rule checks if the given string value contains the path to an existing file.
If set ```true```, only paths to existing files are considered valid.
If set ```false```, all input is considered valid (no validation).

Examples:

```php
$validator = new Validator(['type' => 'string', 'file' => true]);
$validator->validate(''); // -> false
$validator->validate(__DIR__); // -> false
$validator->validate(__FILE__); // -> true
```

## <a name="validation-rules_forbidden"></a>forbidden

The ```forbidden``` validation rule checks if a value is in a given set of forbidden values (enumeration).

Examples:

```php
$validator = new Validator(['type' => 'string', 'forbidden' => ['small', 'medium']]);
$validator->validate('medium'); // -> false
$validator->validate('large'); // -> true
```

The ```forbidden``` validation rule is the opposite of the ```allowed``` validation rule.

## <a name="validation-rules_length"></a>length

The ```length``` validation rule applies to ```string``` typed values only.
The ```length``` validation rule checks if a string has a specified exact length.

Examples:

```php
$validator = new Validator(['type' => 'string', 'length' => 3]);
$validator->validate('foobar'); // -> false
$validator->validate('foo'); // -> true
```

## <a name="validation-rules_max"></a>max

The ```max``` validation rule applies to ```float``` and ```integer``` typed values only.
The ```max``` validation rule checks if a value is equal to or smaller a specified maximum value.

Examples:

```php
$validator = new Validator(['type' => 'integer', 'max' => 5]);
$validator->validate(6); // -> false
$validator->validate(2); // -> true
```

*Note* this validation rule will throw a ```Ares\Exception\InapplicableValidationRuleException``` when used in conjunction with non-supported value types.

## <a name="validation-rules_maxlength"></a>maxlength

The ```maxlength``` validation rule applies to ```string``` typed values only.
The ```maxlength``` validation rule checks if a string does not exceed the given maximum length.

Examples:

```php
$validator = new Validator(['type' => 'string', 'maxlength' => 5]);
$validator->validate('foobar'); // -> false
$validator->validate('foo'); // -> true
```

## <a name="validation-rules_min"></a>min

The ```min``` validation rule applies to ```float``` and ```integer``` typed values only.
The ```min``` validation rule checks if a value is equal to or greater a specified minimum value.

Examples:

```php
$validator = new Validator(['type' => 'integer', 'min' => 5]);
$validator->validate(4); // -> false
$validator->validate(8); // -> true
```

*Note* this validation rule will throw a ```Ares\Exception\InapplicableValidationRuleException``` when used in conjunction with non-supported value types.

## <a name="validation-rules_minlength"></a>minlength

The ```minlength``` validation rule applies to ```string``` typed values only.
The ```minlength``` validation rule checks if a string is not shorter than the given minimum length.

Examples:

```php
$validator = new Validator(['type' => 'string', 'minlength' => 5]);
$validator->validate('foo'); // -> false
$validator->validate('foobar'); // -> true
```

## <a name="validation-rules_nullable"></a>nullable

If set ```true```, ```null``` is considered a valid value.
If set ```false```, ```null``` is considered an invalid value (default).

Examples:

```php
$validator = new Validator(['type' => 'string', 'nullable' => false]);
$validator->validate(null); // -> false
$validator->validate('John Doe'); // -> true

$validator = new Validator(['type' => 'string', 'nullable' => true]);
$validator->validate(null); // -> true
```

The ```nullable``` validation rule may be used in combination with the ```allNullable``` validation option.

## <a name="validation-rules_regex"></a>regex

The ```regex``` validation rule applies to ```string``` typed values only.
The ```regex``` validation rule checks if a string matches a regular expression.

Examples:

```php
$validator = new Validator([
    'type' => 'map',
    'schema' => [
        'key' => [
            'type' => 'string',
            'regex' => '/^[A-Z]{3}$/',
        ],
    ],
]);

$validator->validate(['key' => 'foobar']); // -> false
$validator->validate(['key' => 'FOO']); // -> true
```

## <a name="validation-rules_required"></a>required

Use the ```required``` rule to enforce the presence of a value.
If set ```true```, absent fields are considered invalid.
If set ```false```, absent fields are considered valid (default).

Examples:

```php
$validator = new Validator([
    'type' => 'map',
    'schema' => [
        'name' => ['type' => 'string', 'required' => true],
    ],
]);

$validator->validate([]); // -> false
$validator->validate(['name' => 'John Doe']); // -> true
```

The ```required``` validation rule may be used in combination with the ```allRequired``` validation option.

## <a name="validation-rules_schema"></a>schema

The ```schema``` rule is mandatory when using type ```list```, ```map```, or ```tuple```.

### <a name="validation-rules_schema_schema-list"></a>schema (list)

The validator expects the schema to define a list item's validation rules.

Examples:

```php
$validator = new Validator([
    'type' => 'list',
    'schema' => [
        'type' => 'integer',
    ],
]);

$validator->validate(['foo', 'bar']); // -> false
$validator->validate([1, 2, 3]); // -> true
```

### <a name="validation-rules_schema_schema-map"></a>schema (map)

The validator expects the schema to define per field validation rules for associative array input.

Examples:

```php
$validator = new Validator([
    'type' => 'map',
    'schema' => [
        'email' => ['type' => 'string', 'required' => true],
        'password' => ['type' => 'string', 'required' => true],
    ],
]);

$validator->validate(['email' => 'john.doe@example.com']); // -> false
$validator->validate(['email' => 'john.doe@example.com', 'password' => 'j4n3:)']); // -> true
```

### <a name="validation-rules_schema_schema-tuple"></a>schema (tuple)

The validator expects the schema to define validation rules per input array element.
During validation input array elements are expected to be continuous indexed starting from 0 (0, 1, 2, ...).

Examples:

```php
$validator = new Validator([
    'type' => 'tuple',
    'schema' => [
        ['type' => 'string', 'email' => true],
        ['type' => 'integer'],
    ],
]);

$validator->validate(['john.doe@example.com']); // -> false
$validator->validate([1 => 'john.doe@example.com', 2 => 23]); // -> false
$validator->validate(['john.doe@example.com', 23]); // -> true
```

Internally, all ```schema``` elements of a ```tuple``` are required and cannot be declared optional by schema.

## <a name="validation-rules_type"></a>type

The ```type``` rule is mandatory and defines the expected/allowed value type. Supported types are:

* ```boolean```
* ```float```
* ```integer```
* ```numeric``` (```float``` or ```integer```)
* ```string```
* ```map```
* ```list```
* ```tuple```

Examples:

```php
$validator = new Validator(['type' => 'float']);
$validator->validate(5); // -> false
$validator->validate('John Doe'); // -> false
```

## <a name="validation-rules_url"></a>url

The ```url``` validation rule checks if a value is a valid URL.

Examples:

```php
$validator = new Validator(['type' => 'string', 'url' => true]);
$validator->validate('example'); // -> false
$validator->validate('https://example.com'); // -> true
```

# <a name="custom-validation-messages"></a>Custom Validation Messages

The following example shows how validation error messages can be customized:

```php
// validation rule without custom message (default)
$validator = new Validator([
    'type' => 'integer',
]);

// validation rule with custom message
$validator = new Validator([
    ['type' => 'integer', 'message' => 'Pleaser provide an integer value']
]);
```

Just wrap your rule (key-value) into an array and add a ```'message'``` key.

# <a name="custom-validation-rules"></a>Custom Validation Rules

The following simple example shows how custom validation rules are implemented and integrated:

```php
use Ares\Context;
use Ares\RuleFactory;
use Ares\Rule\AbstractRule;
use Ares\Schema\Type;
use Ares\Validator;

class ZipCodeRule extends AbstractRule
{
    public const ID = 'zipcode';
    public const ERROR_MESSAGE = 'Invalid ZIP code';

    /**
     * Returns all supported value types.
     *
     * @return array
     */
    public function getSupportedTypes(): array
    {
        return [
            Type::STRING,
        ];
    }

    /**
     * Perform the value validation.
     *
     * @param mixed         $args    Validation rule arguments.
     * @param mixed         $data    Data being validated.
     * @param \Ares\Context $context Validation context.
     * @return bool
     */
    public function performValidation($args, $data, Context $context): bool
    {
        // implement validation ...

        // add error if the validation fails
        $context->addError(self::ID, self::ERROR_MESSAGE);

        // TRUE  - skip all following validation rules for the current field
        // FALSE - run all following validation rules for the current field
        return false; 
    }
}

$ruleFactory = new RuleFactory();
$ruleFactory->set(ZipCodeRule::ID, new ZipCodeRule());

$schema = [
    'type' => 'string',
    'zipcode' => true,
];

$validator = new Validator($schema, [], $ruleFactory);
```

