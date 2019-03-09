# Ares

Ares is a lightweight standalone validation library.

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


## Validation Rules

The validator uses a default set of validation rules which are applied to the provided schema during validator construction:

```php
Validator::SCHEMA_DEFAULTS = [
    'required' => false,
    'blankable' => false,
];
```

### blankable

The ```blankable``` rule applies to ```string``` typed values only.
If set ```true```, blank strings are considered valid.
If set ```false```, blank strings are considered invalid.

Examples:

```php
$validator = new Validator(['type' => 'string', 'blankable' => false]);
$validator->validate(''); // -> false
$validator->validate('   '); // -> false
$validator->validate('John Doe'); // -> true

$validator = new Validator(['type' => 'string', 'blankable' => true]);
$validator->validate('   '); // -> true
```

### required

Use the ```required``` rule to enforce the presence of a value.
If set ```true```, values must not be ```null```.
If set ```false```, values are allowed to be ```null```.

Examples:

```php
$validator = new Validator(['type' => 'integer', 'required' => true]);
$validator->validate(null); // -> false

$validator = new Validator(['type' => 'integer', 'required' => false]);
$validator->validate(null); // -> true
```

### schema (map)

The ```schema``` rule is mandatory when using type ```map```. The validator expects the schema to define per field validation rules for associative array input.

Example:

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

