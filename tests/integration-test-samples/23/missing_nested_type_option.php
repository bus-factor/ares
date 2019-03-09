<?php

declare(strict_types=1);

/**
 * missing_nested_type_option.php
 *
 * @author Michael Leßnau <michael.lessnau@gmail.com>
 * @since  2019-03-09
 */

use Ares\Exception\InvalidValidationSchemaException;
use Ares\Validation\Validator;

$schema = [
    'type' => 'map',
    'schema' => [
        'name' => [],
    ],
];

$validator = new Validator($schema);

$this->expectException(InvalidValidationSchemaException::class);
$this->expectExceptionMessage('Missing schema option: $schema[\'schema\'][\'name\'][\'type\']');

$validator->validate(['name' => 'John Doe']);

