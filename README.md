# Ares

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

use Ares\Validation\Validator;

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

Each ```Ares\Validation\Error``` object implements the ```JsonSerializable``` interface and contains details about the error.

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

### schema (map)

The ```schema``` rule is mandatory when using type ```map```. The validator expects the schema to define per field validation rules for associative array input.

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

The ```type``` rule defines the expected/allowed value type. Supported types are:

* ```boolean```
* ```float```
* ```integer```
* ```string```
* ```map```

Examples:

```php
$validator = new Validator(['type' => 'float']);
$validator->validate(5); // -> false
$validator->validate('John Doe'); // -> false
```

