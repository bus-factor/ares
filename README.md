```
  __ _ _ __ ___  ___
 / _` | '__/ _ \/ __|
| (_| | | |  __/\__ \
 \__,_|_|  \___||___/
```

Ares is a lightweight standalone validation library.

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/fadd1213ef8e402cb963d8be8f45dcda)](https://app.codacy.com/app/bus-factor/ares?utm_source=github.com&utm_medium=referral&utm_content=bus-factor/ares&utm_campaign=Badge_Grade_Dashboard)
[![Latest Stable Version](https://img.shields.io/packagist/v/bus-factor/ares.svg?style=flat-square)](https://packagist.org/packages/bus-factor/ares)
[![Total Downloads](https://poser.pugx.org/bus-factor/ares/downloads.png)](https://packagist.org/packages/bus-factor/ares)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.2-8892BF.svg?style=flat-square)](https://php.net/)
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
  * [allUnknownAllowed](#validation-options_all-unknown-allowed)
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
  * [unknownAllowed](#validation-rules_unknownAllowed)
  * [url](#validation-rules_url)
* [Custom Types](#custom-types)
* [Custom Validation Error Messages](#custom-validation-error-messages)
  * [Change the Validation Error Message of a single Rule](#custom-validation-error-messages-per-field)
  * [Localization of Validation Error Messages](#custom-validation-error-messages-localization)
* [Custom Validation Rules](#custom-validation-rules)
* [Sanitization](#sanitization)
  * [Sanitization Options](#sanitization-options)

# <a name="installation"></a>Installation

Install the library via composer:
```
composer require bus-factor/ares
```

# <a name="basic-usage"></a>Basic Usage

```php
<?php

use Ares\Ares;

// atomic types
$ares = new Ares(['type' => 'string']);
$valid = $ares->validate('John Doe');
$errors = $ares->getValidationErrors();

// complex/nested types
$ares = new Ares([
    'type' => 'map',
    'schema' => [
        'firstName' => ['type' => 'string', 'required' => true],
        'lastName' => ['type' => 'string', 'required' => true],
    ],
]);
$valid = $ares->validate(['firstName' => 'John', 'lastName' => 'Doe']);
$errors = $ares->getValidationErrors();
```

# <a name="validation-errors"></a>Validation Errors

The ```validate()``` method returns ```true``` if the provided data is valid, otherwise ```false```.

The ```getErrors()``` method returns a list of validation errors that occurred during the last data validation.
The list of validation errors is reset each time ```validate()``` is called.

Each ```Ares\Error\Error``` object implements the ```JsonSerializable``` interface and contains details about the error.

# <a name="validation-options"></a>Validation Options

Validation options may be passed on validation:

```php
$schema = [];
$options = [];
$ares = new Ares($schema, $options);
```

Default validation options are:

```php
Validator::OPTIONS_DEFAULTS = [
    'allBlankable'      => false,
    'allNullable'       => false,
    'allRequired'       => true,
    'allUnknownAllowed' => false,
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

$ares = new Ares($schema);

$ares->validate(['name' => ''], ['allBlankable' => true]); // -> true
$ares->validate(['name' => ''], ['allBlankable' => false]); // -> false
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

$ares->validate(['name' => 'John Doe', 'email' => ''], ['allBlankable' => false]); // -> true
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

$ares->validate(['name' => null], ['allNullable' => true]); // -> true
$ares->validate(['name' => null], ['allNullable' => false]); // -> false
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

$ares->validate(['name' => 'John Doe', 'email' => null], ['allNullable' => false]); // -> true
```

## <a name="validation-options_all-required"></a>allRequired

If set ```true``` (default) fields that are defined in the schema and not present in the input, are considered invalid.
If set ```false``` fields that are defined in the schema and not present in the input, are considered valid.

```php
$schema = [
    'type' => 'map',
    'schema' => [
        'name' => ['type' => 'string'],
    ],
];

$ares = new Ares($schema);
$ares->validate([], ['allRequired' => true]); // -> false
$ares->validate([], ['allRequired' => false]); // -> true
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

$ares = new Ares($schema);
$ares->validate(['name' => 'John Doe'], ['allRequired' => true]); // -> true
```

## <a name="validation-options_all-unknown-allowed"></a>allUnknownAllowed

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

$ares = new Ares($schema);
$ares->validate(['name' => 'John Doe', 'initials' => 'JD'], ['allUnknownAllowed' => false]); // -> false
$ares->validate(['name' => 'John Doe', 'initials' => 'JD'], ['allUnknownAllowed' => true]); // -> true
```

# <a name="validation-rules"></a>Validation Rules

## <a name="validation-rules_allowed"></a>allowed

The ```allowed``` validation rule checks if a value is in a given set of allowed values (enumeration).

Examples:

```php
$ares = new Ares(['type' => 'string', 'allowed' => ['small', 'large']]);
$ares->validate('medium'); // -> false
$ares->validate('small'); // -> true
```

The ```allowed``` validation rule is the opposite of the ```forbidden``` validation rule.

## <a name="validation-rules_blankable"></a>blankable

The ```blankable``` rule applies to ```string``` typed values only.
If set ```true```, blank strings are considered valid.
If set ```false```, blank strings are considered invalid (default).

Examples:

```php
$ares = new Ares(['type' => 'string', 'blankable' => false]);
$ares->validate(''); // -> false
$ares->validate('   '); // -> false
$ares->validate('John Doe'); // -> true

$ares = new Ares(['type' => 'string', 'blankable' => true]);
$ares->validate('   '); // -> true
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
$ares = new Ares(['type' => 'string', 'datetime' => true]);
$ares->validate('foo'); // -> false
$ares->validate('2018-03-23'); // -> true

$ares = new Ares(['type' => 'string', 'datetime' => 'd.m.Y H:i']);
$ares->validate('2018-03-23'); // -> false
$ares->validate('23.03.2019 00:20'); // -> true
```

## <a name="validation-rules_directory"></a>directory

The ```directory``` validation rule checks if the given string value contains the path to an existing directory.
If set ```true```, only paths to existing directories are considered valid.
If set ```false```, all input is considered valid (no validation).

Examples:

```php
$ares = new Ares(['type' => 'string', 'directory' => true]);
$ares->validate(''); // -> false
$ares->validate(__FILE__); // -> false
$ares->validate(__DIR__); // -> true
```

## <a name="validation-rules_email"></a>email

The ```email``` validation rule checks if a value is a valid email address.
If set ```true```, only valid email addresses are considered valid.
If set ```false```, all input is considered valid (no validation).

Examples:

```php
$ares = new Ares(['type' => 'string', 'email' => true]);
$ares->validate('John Doe'); // -> false
$ares->validate('john.doe@example.com'); // -> true
```

## <a name="validation-rules_file"></a>file

The ```file``` validation rule checks if the given string value contains the path to an existing file.
If set ```true```, only paths to existing files are considered valid.
If set ```false```, all input is considered valid (no validation).

Examples:

```php
$ares = new Ares(['type' => 'string', 'file' => true]);
$ares->validate(''); // -> false
$ares->validate(__DIR__); // -> false
$ares->validate(__FILE__); // -> true
```

## <a name="validation-rules_forbidden"></a>forbidden

The ```forbidden``` validation rule checks if a value is in a given set of forbidden values (enumeration).

Examples:

```php
$ares = new Ares(['type' => 'string', 'forbidden' => ['small', 'medium']]);
$ares->validate('medium'); // -> false
$ares->validate('large'); // -> true
```

The ```forbidden``` validation rule is the opposite of the ```allowed``` validation rule.

## <a name="validation-rules_length"></a>length

The ```length``` validation rule applies to ```string``` and ```list``` typed values.
The ```length``` validation rule checks if a string, or list has a specified exact length.

Examples:

```php
$ares = new Ares(['type' => 'string', 'length' => 3]);
$ares->validate('foobar'); // -> false
$ares->validate('foo'); // -> true

$ares = new Ares([
    'type' => 'list',
    'length' => 3,
    'schema' => [
        'type' => 'integer'
    ],
])
$ares->validate([1, 2]); // -> false
$ares->validate([1, 2, 3]); // -> true

```

## <a name="validation-rules_max"></a>max

The ```max``` validation rule applies to ```float``` and ```integer``` typed values only.
The ```max``` validation rule checks if a value is equal to or smaller a specified maximum value.

Examples:

```php
$ares = new Ares(['type' => 'integer', 'max' => 5]);
$ares->validate(6); // -> false
$ares->validate(2); // -> true
```

*Note* this validation rule will throw a ```Ares\Exception\InapplicableValidationRuleException``` when used in conjunction with non-supported value types.

## <a name="validation-rules_maxlength"></a>maxlength

The ```maxlength``` validation rule applies to ```string``` and ```list``` typed values.
The ```maxlength``` validation rule checks if a string, or list does not exceed a specified maximum length.

Examples:

```php
$ares = new Ares(['type' => 'string', 'maxlength' => 5]);
$ares->validate('foobar'); // -> false
$ares->validate('foo'); // -> true

$ares = new Ares([
    'type' => 'list',
    'maxlength' => 3,
    'schema' => [
        'type' => 'integer'
    ],
])
$ares->validate([1, 2, 3, 4]); // -> false
$ares->validate([1, 2, 3]); // -> true
```

## <a name="validation-rules_min"></a>min

The ```min``` validation rule applies to ```float``` and ```integer``` typed values only.
The ```min``` validation rule checks if a value is equal to or greater a specified minimum value.

Examples:

```php
$ares = new Ares(['type' => 'integer', 'min' => 5]);
$ares->validate(4); // -> false
$ares->validate(8); // -> true
```

*Note* this validation rule will throw a ```Ares\Exception\InapplicableValidationRuleException``` when used in conjunction with non-supported value types.

## <a name="validation-rules_minlength"></a>minlength

The ```minlength``` validation rule applies to ```string``` and ```list``` typed values.
The ```minlength``` validation rule checks if a string, or list is not shorter than a specified minimum length.

Examples:

```php
$ares = new Ares(['type' => 'string', 'minlength' => 5]);
$ares->validate('foo'); // -> false
$ares->validate('foobar'); // -> true

$ares = new Ares([
    'type' => 'list',
    'minlength' => 3,
    'schema' => [
        'type' => 'integer'
    ],
])
$ares->validate([1, 2]); // -> false
$ares->validate([1, 2, 3]); // -> true
```

## <a name="validation-rules_nullable"></a>nullable

If set ```true```, ```null``` is considered a valid value.
If set ```false```, ```null``` is considered an invalid value (default).

Examples:

```php
$ares = new Ares(['type' => 'string', 'nullable' => false]);
$ares->validate(null); // -> false
$ares->validate('John Doe'); // -> true

$ares = new Ares(['type' => 'string', 'nullable' => true]);
$ares->validate(null); // -> true
```

The ```nullable``` validation rule may be used in combination with the ```allNullable``` validation option.

## <a name="validation-rules_regex"></a>regex

The ```regex``` validation rule applies to ```string``` typed values only.
The ```regex``` validation rule checks if a string matches a regular expression.

Examples:

```php
$ares = new Ares([
    'type' => 'map',
    'schema' => [
        'key' => [
            'type' => 'string',
            'regex' => '/^[A-Z]{3}$/',
        ],
    ],
]);

$ares->validate(['key' => 'foobar']); // -> false
$ares->validate(['key' => 'FOO']); // -> true
```

## <a name="validation-rules_required"></a>required

Use the ```required``` rule to enforce the presence of a value.
If set ```true```, absent fields are considered invalid.
If set ```false```, absent fields are considered valid (default).

Examples:

```php
$ares = new Ares([
    'type' => 'map',
    'schema' => [
        'name' => ['type' => 'string', 'required' => true],
    ],
]);

$ares->validate([]); // -> false
$ares->validate(['name' => 'John Doe']); // -> true
```

The ```required``` validation rule may be used in combination with the ```allRequired``` validation option.

## <a name="validation-rules_schema"></a>schema

The ```schema``` rule is mandatory when using type ```list```, ```map```, or ```tuple```.

### <a name="validation-rules_schema_schema-list"></a>schema (list)

The validator expects the schema to define a list item's validation rules.

Examples:

```php
$ares = new Ares([
    'type' => 'list',
    'schema' => [
        'type' => 'integer',
    ],
]);

$ares->validate(['foo', 'bar']); // -> false
$ares->validate([1, 2, 3]); // -> true
```

### <a name="validation-rules_schema_schema-map"></a>schema (map)

The validator expects the schema to define per field validation rules for associative array input.

Examples:

```php
$ares = new Ares([
    'type' => 'map',
    'schema' => [
        'email' => ['type' => 'string', 'required' => true],
        'password' => ['type' => 'string', 'required' => true],
    ],
]);

$ares->validate(['email' => 'john.doe@example.com']); // -> false
$ares->validate(['email' => 'john.doe@example.com', 'password' => 'j4n3:)']); // -> true
```

### <a name="validation-rules_schema_schema-tuple"></a>schema (tuple)

The validator expects the schema to define validation rules per input array element.
During validation input array elements are expected to be continuous indexed starting from 0 (0, 1, 2, ...).

Examples:

```php
$ares = new Ares([
    'type' => 'tuple',
    'schema' => [
        ['type' => 'string', 'email' => true],
        ['type' => 'integer'],
    ],
]);

$ares->validate(['john.doe@example.com']); // -> false
$ares->validate([1 => 'john.doe@example.com', 2 => 23]); // -> false
$ares->validate(['john.doe@example.com', 23]); // -> true
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
$ares = new Ares(['type' => 'float']);
$ares->validate(5); // -> false
$ares->validate('John Doe'); // -> false
```

Read the section [Custom Types](#custom-types) to find out how to define and reuse your own types.

## <a name="validation-rules_unknownAllowed"></a>unknownAllowed

The ```unknownAllowed``` validation rule checks if a ```map``` contains fields that are not defined in the schema.
If set ```true```, fields that are not defined in the schema are considered valid.
If set ```false```, fields that are not defined in the schema are considered invalid.

Examples:

```php
$ares = new Ares([
    'type' => 'map',
    'schema' => [
        'name' => ['type' => 'string'],
    ],
    'unknownAllowed' => false,
]);

$ares->validate(['name' => 'John Doe', 'email' => 'john.doe@example.com']); // -> false
$ares->validate(['name' => 'John Doe']); // -> true
```

## <a name="validation-rules_url"></a>url

The ```url``` validation rule checks if a value is a valid URL.

Examples:

```php
$ares = new Ares(['type' => 'string', 'url' => true]);
$ares->validate('example'); // -> false
$ares->validate('https://example.com'); // -> true
```

# <a name="custom-types"></a>Custom Types

Basically, a custom type is a user defined schema that is stored in and retrieved from a registry.
Here's an example how it works:

```php
use Ares\Ares;
use Ares\Schema\TypeRegistry;

TypeRegistry::register('GermanDateString', [
    'type' => 'string',
    ['datetime' => 'd.m.Y', 'message' => 'Invalid date format, try something like "24.02.2019"'],
]);

TypeRegistry::register('ListOfHobbies', [
    'type' => 'list',
    'schema' => [
        'type' => 'string',
        'allowed' => ['Reading', 'Biking'],
    ],
]);

TypeRegistry::register('Student', [
    'type' => 'map',
    'schema' => [
        'birthDate' => ['type' => 'GermanDateString'],
        'hobbies' => ['type' => 'ListOfHobbies', 'minlength' => 1],
    ],
]);

$schema = ['type' => 'Student'];

$ares = new Ares($schema);

$ares->validate(['birthDate' => '1998-06-14', 'hobbies' => []]); // false
$ares->validate(['birthDate' => '14.06.1998', 'hobbies' => ['Reading']]); // true
```

Previously registered types are unregistered using ```TypeRegistry::unregister()```.
All priviously registered types are unregistered at once using ```TypeRegistry::unregisterAll()```.
It is also possible to define recursive types.

# <a name="custom-validation-error-messages"></a>Custom Validation Error Messages

## <a name="custom-validation-error-messages-per-field"></a>Change the Validation Error Message of a single Rule

The following example shows how validation error messages can be customized:

```php
// validation rule without custom message (default)
$ares = new Ares([
    'type' => 'integer',
]);

// validation rule with custom message
$ares = new Ares([
    ['type' => 'integer', 'message' => 'Pleaser provide an integer value']
]);
```

Just wrap your rule (key-value) into an array and add a ```'message'``` key.

## <a name="custom-validation-error-messages-localization"></a>Localization of Validation Error Messages

All built-in validation rules use the ```Ares\Error\ErrorMessageRendererInterface``` to render the messages.
If not specified, an instance of ```Ares\Error\ErrorMessageRenderer``` is created and passed to the validation process.
If necessary, a custom error message renderer can be passed to the validator:

```php
use Ares\Ares;
use Ares\Validation\Error\ErrorMessageRendererInterface;

class MyErrorMessageRenderer implements ErrorMessageRendererInterface
{
    // ...
}

// ...

$ares = new Ares($schema);

$ares->getValidator()->setErrorMessageRenderer(new MyErrorMessageRenderer());

$valid = $ares->validate($data);
```

# <a name="custom-validation-rules"></a>Custom Validation Rules

The following simple example shows how custom validation rules are implemented and integrated:

```php
use Ares\Ares;
use Ares\Schema\Type;
use Ares\Validation\Context;
use Ares\Validation\RuleRegistry;
use Ares\Validation\Rule\AbstractRule;

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
     * @param mixed   $args    Validation rule arguments.
     * @param mixed   $data    Data being validated.
     * @param Context $context Validation context.
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

RuleRegistry::register(ZipCodeRule::ID, new ZipCodeRule());

$schema = [
    'type' => 'string',
    'zipcode' => true,
];

$ares = new Ares($schema);
```

# <a name="sanitization"></a>Sanitization

This following example shows how to sanitize data:

```php
$schema = [
    'type' => 'map',
    'schema' => [
        'name' => ['type' => 'string'],
        'age' => ['type' => 'integer'],
        'active' => ['type' => 'boolean'],
    ],
];

$ares = new Ares($schema);

$data = [
    'name' => ' John Doe   ',
    'age' => '23',
    'active' => '1',
    'hobby' => 'Reading',
];

$sanitizedData = $ares->sanitize($data);

// Result:
// [
//     'name' => 'John Doe',
//     'age' => 23,
//     'active' => true,
// ]
```

As shown in the example, by default sanitization makes these adjustments:
* Trim strings
* Convert numeric strings into integer, or string values
* Convert numeric non-empty strings into boolean values
* Removes unknown fields from the input data

## <a name="sanitization-options"></a>Sanitization Options

### trimStrings

If set ```true``` (default) sorrounding whitespace will be removed from strings.
If set ```false``` sorrounding whitespace will be preserved.

### purgeUnknown

If set ```true``` (default) unknown fields (fields/indices not defined in the schema) will be removed from the input data.
If set ```false``` unknown fields will be preserved.

