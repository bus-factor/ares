# Ares

Ares is a lightweight standalone validation library.

## Basic Usage

```php
<?php

use Ares\Validation\Validator;

$validator = new Validator(['type' => 'string', 'required' => true]);
$valid = $validator->validate($data);
$errors = $validator->getErrors();
```

## Validation Errors

The ```validate()``` method returns ```true``` if the provided data is valid, otherwise ```false```.

The ```getErrors()``` method returns a list of validation errors that occurred during the last data validation.
The list of validation errors is reset each time ```validate()``` is called.

Each ```Ares\Validation\Error``` object implements the ```JsonSerializable``` interface and contains details about the error.


## Validation Rules

### blankable

The ```blankable``` rule applies to ```string``` typed values only.
If set ```true```, blank strings are considered valid.
If set ```false```, blank strings are considered invalid.

Examples:

```php
$validator = new Validator(['type' => 'string', 'required' => true, 'blankable' => false]);
$validator->validate(''); // -> false
$validator->validate('   '); // -> false
$validator->validate('John Doe'); // -> true

$validator = new Validator(['type' => 'string', 'required' => true, 'blankable' => true]);
$validator->validate('   '); // -> true
```

### required

Use the ```required``` rule to enforce the presence of value.
If set ```true```, values must not be ```null```.
If set ```false```, values are allowed to be ```null```.

Examples:

```php
$validator = new Validator(['type' => 'integer', 'required' => true]);
$validator->validate(null); // -> false

$validator = new Validator(['type' => 'integer', 'required' => false]);
$validator->validate(null); // -> true
```

### type

The ```type``` rule defines the expected/allowed value type. Supported types are:

* ```boolean```
* ```float```
* ```integer```
* ```string```

Examples:

```php
$validator = new Validator(['type' => 'float']);
$validator->validate(5); // -> false
$validator->validate('John Doe'); // -> false
```

