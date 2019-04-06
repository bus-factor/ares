```
  __ _ _ __ ___  ___
 / _` | '__/ _ \/ __|
| (_| | | |  __/\__ \
 \__,_|_|  \___||___/
```

Ares is a lightweight standalone validation library.

![](https://travis-ci.com/bus-factor/ares.svg?token=6CVThNyY94qpVvuMgX3F&branch=master)

## Installation

Install the library via composer:
```
composer require bus-factor/ares
```

## Basic Usage

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

## Validation Errors

The ```validate()``` method returns ```true``` if the provided data is valid, otherwise ```false```.

The ```getErrors()``` method returns a list of validation errors that occurred during the last data validation.
The list of validation errors is reset each time ```validate()``` is called.

Each ```Ares\Error``` object implements the ```JsonSerializable``` interface and contains details about the error.

## Validation Options

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

### allBlankable

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

### allNullable

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

### allRequired

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

### allowUnknown

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

## Validation Rules

### allowed

The ```allowed``` validation rule checks if a value is in a given set of allowed values (enumeration).

Examples:

```php
$validator = new Validator(['type' => 'string', 'allowed' => ['small', 'large']]);
$validator->validate('medium'); // -> false
$validator->validate('small'); // -> true
```

The ```allowed``` validation rule is the opposite of the ```forbidden``` validation rule.

### blankable

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

### datetime

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

### email

The ```email``` validation rule checks if a value is a valid email address.

Examples:

```php
$validator = new Validator(['type' => 'string', 'email' => true]);
$validator->validate('John Doe'); // -> false
$validator->validate('john.doe@example.com'); // -> true
```

### forbidden

The ```forbidden``` validation rule checks if a value is in a given set of forbidden values (enumeration).

Examples:

```php
$validator = new Validator(['type' => 'string', 'forbidden' => ['small', 'medium']]);
$validator->validate('medium'); // -> false
$validator->validate('large'); // -> true
```

The ```forbidden``` validation rule is the opposite of the ```allowed``` validation rule.

### max

The ```max``` validation rule applies to ```float``` and ```integer``` typed values only.
The ```max``` validation rule checks if a value is equal to or smaller a specified maximum value.

Examples:

```php
$validator = new Validator(['type' => 'integer', 'max' => 5]);
$validator->validate(6); // -> false
$validator->validate(2); // -> true
```

*Note* this validation rule will throw a ```Ares\Exception\InapplicableValidationRuleException``` when used in conjunction with non-supported value types.

### maxlength

The ```maxlength``` validation rule applies to ```string``` typed values only.
The ```maxlength``` validation rule checks if a string does not exceed the given maximum length.

Examples:

```php
$validator = new Validator(['type' => 'string', 'maxlength' => 5]);
$validator->validate('foobar'); // -> false
$validator->validate('foo'); // -> true
```

### min

The ```min``` validation rule applies to ```float``` and ```integer``` typed values only.
The ```min``` validation rule checks if a value is equal to or greater a specified minimum value.

Examples:

```php
$validator = new Validator(['type' => 'integer', 'min' => 5]);
$validator->validate(4); // -> false
$validator->validate(8); // -> true
```

*Note* this validation rule will throw a ```Ares\Exception\InapplicableValidationRuleException``` when used in conjunction with non-supported value types.

### minlength

The ```minlength``` validation rule applies to ```string``` typed values only.
The ```minlength``` validation rule checks if a string is not shorter than the given minimum length.

Examples:

```php
$validator = new Validator(['type' => 'string', 'minlength' => 5]);
$validator->validate('foo'); // -> false
$validator->validate('foobar'); // -> true
```

### nullable

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

### regex

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

### required

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

### schema

The ```schema``` rule is mandatory when using type ```list```, or ```map```.

#### schema (list)

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

#### schema (map)

The validator expects the schema to define per field validation rules for associative array input.

Examples:

```php
$validator = new Validator([
    'type' => 'map',
    'required' => true,
    'schema' => [
        'email' => ['type' => 'string', 'required' => true],
        'password' => ['type' => 'string', 'required' => true],
    ],
]);

$validator->validate(['email' => 'john.doe@example.com']); // -> false
$validator->validate(['email' => 'john.doe@example.com', 'password' => 'j4n3:)']); // -> true
```

### type

The ```type``` rule is mandatory and defines the expected/allowed value type. Supported types are:

* ```boolean```
* ```float```
* ```integer```
* ```string```
* ```map```
* ```list```

Examples:

```php
$validator = new Validator(['type' => 'float']);
$validator->validate(5); // -> false
$validator->validate('John Doe'); // -> false
```

### url

The ```url``` validation rule checks if a value is a valid URL.

Examples:

```php
$validator = new Validator(['type' => 'string', 'url' => true]);
$validator->validate('example'); // -> false
$validator->validate('https://example.com'); // -> true
```

## Custom validation rules

The following simple example shows how custom validation rules are implemented and integrated:

```php
use Ares\RuleFactory;
use Ares\Rule\RuleInterface;
use Ares\Validator;

class ZipCodeRule implements RuleInterface
{
    const ID = 'zipcode';
    const ERROR_MESSAGE = 'Invalid ZIP code';

    public function validate($config, $data, Context $context): bool
    {
        // implement validation ...

        // add error if the validation fails
        $context->addError(self::ID, self::ERROR_MESSAGE);

        // skip all following validation rules for the current field
        return false; 
    }
}

$ruleFactory = new RuleFactory();
$ruleFactory->set(ZipCodeRule::ID, new ZipCodeRule());

$schema = [
    'type' => 'string',
    'zipcode' => true,
];

$validator = new Validator($schema, [], null, $ruleFactory);
```

